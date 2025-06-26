<?php

namespace App\Libraries\Audit;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait Auditable
{
    protected ?string $auditMessage = null;
    
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            if ($model->shouldAudit()) {
                AuditLogger::created($model, $model->getAuditMessage());
            }
        });

        static::updated(function ($model) {
            if ($model->shouldAudit()) {
                AuditLogger::updated($model, $model->getAuditMessage());
            }
        });

        static::deleted(function ($model) {
            if ($model->shouldAudit()) {
                AuditLogger::deleted($model, $model->getAuditMessage());
            }
        });
    }

    /**
     * Override attach method to log many-to-many attachments
     */
    public function attach($id, array $attributes = [], $touch = true)
    {
        $relation = $this->getRelationFromBacktrace();
        
        if ($relation && $this->shouldAudit()) {
            $result = parent::attach($id, $attributes, $touch);
            AuditLogger::attached($this, $relation, $id, $attributes, $this->getAuditMessage());
            return $result;
        }
        
        return parent::attach($id, $attributes, $touch);
    }

    /**
     * Override detach method to log many-to-many detachments
     */
    public function detach($ids = null, $touch = true)
    {
        $relation = $this->getRelationFromBacktrace();
        
        if ($relation && $this->shouldAudit()) {
            $result = parent::detach($ids, $touch);
            AuditLogger::detached($this, $relation, $ids, $this->getAuditMessage());
            return $result;
        }
        
        return parent::detach($ids, $touch);
    }

    /**
     * Override sync method to log many-to-many sync operations
     */
    public function sync($ids, $detaching = true)
    {
        $relation = $this->getRelationFromBacktrace();
        
        if ($relation && $this->shouldAudit()) {
            $changes = parent::sync($ids, $detaching);
            AuditLogger::synced($this, $relation, $changes, $this->getAuditMessage());
            return $changes;
        }
        
        return parent::sync($ids, $detaching);
    }

    /**
     * Set a custom audit message for the next operation
     */
    public function setAuditMessage(string $message): self
    {
        $this->auditMessage = $message;
        return $this;
    }

    /**
     * Get the current audit message
     */
    public function getAuditMessage(): ?string
    {
        $message = $this->auditMessage;
        $this->auditMessage = null; // Clear after getting
        return $message;
    }

    /**
     * Perform an operation with a custom audit message
     */
    public function withAuditMessage(string $message, \Closure $callback)
    {
        $this->setAuditMessage($message);
        return $callback();
    }

    /**
     * Get the relation name from backtrace
     */
    protected function getRelationFromBacktrace(): ?string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        
        foreach ($trace as $frame) {
            if (isset($frame['object']) && $frame['object'] instanceof BelongsToMany) {
                return $frame['function'];
            }
        }
        
        return null;
    }

    /**
     * Disable auditing for this model instance
     */
    public function withoutAuditing(\Closure $callback)
    {
        $originalListeners = $this->getEventDispatcher();
        static::unsetEventDispatcher();
        
        try {
            return $callback();
        } finally {
            static::setEventDispatcher($originalListeners);
        }
    }

    /**
     * Check if model should be audited
     */
    public function shouldAudit(): bool
    {
        return property_exists($this, 'auditEnabled') ? $this->auditEnabled : true;
    }
}