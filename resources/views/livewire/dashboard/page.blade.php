<section class="w-full" x-data="dashboardCharts()" x-init="initializeAllCharts()">
    <x-contents.heading title="Healthcare Dashboard" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8 space-y-6">

            <!-- Summary Cards Row 1 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Patients Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Patients</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalPatients) }}</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Appointments Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Appointments</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalAppointments) }}</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Appointments Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending Appointments</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($pendingAppointments) }}</p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Completed Appointments Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Completed Appointments</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($completedAppointments) }}</p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards Row 2 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Today's Appointments -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Today's Appointments</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($todayAppointments) }}</p>
                        </div>
                        <div class="bg-indigo-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Consultations -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-pink-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Consultations</p>
                            <p class="text-3xl font-bold text-gray-900">{{
                                number_format($consultationMetrics['total_consultations']) }}</p>
                        </div>
                        <div class="bg-pink-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Today's Consultations -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-emerald-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Today's Consultations</p>
                            <p class="text-3xl font-bold text-gray-900">{{
                                number_format($consultationMetrics['consultations_today']) }}</p>
                        </div>
                        <div class="bg-emerald-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- This Week's Consultations -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">This Week's Consultations</p>
                            <p class="text-3xl font-bold text-gray-900">{{
                                number_format($consultationMetrics['consultations_this_week']) }}</p>
                        </div>
                        <div class="bg-orange-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Monthly Appointments Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Appointments</h3>
                    <div id="monthlyAppointmentsChart"></div>
                </div>

                <!-- Appointment Status Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Status Distribution</h3>
                    <div id="appointmentStatusChart"></div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Monthly Patients Growth Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Patient Registration</h3>
                    <div id="patientGrowthChart"></div>
                </div>

                <!-- Top Doctors Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Doctors by Consultations</h3>
                    <div id="topDoctorsChart"></div>
                </div>
            </div>

            <!-- Charts Row 3 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Patient Age Distribution Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient Age Distribution</h3>
                    <div id="patientAgeChart"></div>
                </div>

                <!-- Appointments by Time Slot Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointments by Time Slot</h3>
                    <div id="timeSlotChart"></div>
                </div>
            </div>

            <!-- Consultation Metrics -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Consultation Metrics</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">Average Consultation Time</span>
                        <span class="text-2xl font-bold text-gray-900">{{
                            $consultationMetrics['avg_consultation_time'] }} min</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">Today's Consultations</span>
                        <span class="text-2xl font-bold text-blue-900">{{
                            $consultationMetrics['consultations_today'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">This Week's Total</span>
                        <span class="text-2xl font-bold text-green-900">{{
                            $consultationMetrics['consultations_this_week'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-purple-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">Total Consultations</span>
                        <span class="text-2xl font-bold text-purple-900">{{
                            $consultationMetrics['total_consultations'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments Table -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Appointments</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Queue #</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Patient</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Appointment Date</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentAppointments as $appointment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $appointment['queue_number'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $appointment['patient_name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $appointment['status_class'] }}">
                                        {{ $appointment['status_display'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $appointment['appointment_date'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $appointment['created_at'] }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-contents.layout>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    function dashboardCharts() {
       return {
           charts: {},
           
           initializeAllCharts() {
               this.$nextTick(() => {
                   this.initMonthlyAppointmentsChart();
                   this.initAppointmentStatusChart();
                   this.initTopDoctorsChart();
                   this.initPatientAgeChart();
                   this.initTimeSlotChart();
                   this.initPatientGrowthChart();
                   console.log('All healthcare dashboard charts initialized');
               });
           },

           destroyChart(chartKey) {
               if (this.charts[chartKey]) {
                   this.charts[chartKey].destroy();
                   delete this.charts[chartKey];
               }
           },

           initMonthlyAppointmentsChart() {
               this.destroyChart('monthlyAppointments');
               
               const monthlyAppointmentsOptions = {
                   series: [{
                       name: 'Appointments',
                       data: @json($monthlyAppointmentsData['data'])
                   }],
                   chart: {
                       type: 'bar',
                       height: 350,
                       toolbar: { show: false }
                   },
                   plotOptions: {
                       bar: {
                           borderRadius: 4,
                           horizontal: false,
                           columnWidth: '55%',
                       }
                   },
                   dataLabels: { enabled: false },
                   stroke: {
                       show: true,
                       width: 2,
                       colors: ['transparent']
                   },
                   xaxis: {
                       categories: @json($monthlyAppointmentsData['categories']),
                       labels: { style: { fontSize: '12px' } }
                   },
                   yaxis: {
                       title: { text: 'Number of Appointments' }
                   },
                   fill: {
                       opacity: 1,
                       colors: ['#3B82F6']
                   },
                   tooltip: {
                       y: {
                           formatter: function (val) {
                               return val + " appointments"
                           }
                       }
                   },
                   grid: {
                       borderColor: '#f3f4f6',
                       strokeDashArray: 3
                   }
               };

               if (document.querySelector("#monthlyAppointmentsChart")) {
                   this.charts.monthlyAppointments = new ApexCharts(document.querySelector("#monthlyAppointmentsChart"), monthlyAppointmentsOptions);
                   this.charts.monthlyAppointments.render();
               }
           },

           initAppointmentStatusChart() {
               this.destroyChart('appointmentStatus');
               
               const appointmentStatusOptions = {
                   series: @json($appointmentStatusData['data']),
                   chart: {
                       type: 'pie',
                       height: 350
                   },
                   labels: @json($appointmentStatusData['labels']),
                   colors: ['#F59E0B', '#10B981', '#EF4444', '#8B5CF6', '#06B6D4'],
                   legend: { position: 'bottom' },
                   tooltip: {
                       y: {
                           formatter: function (val) {
                               return val + " appointments"
                           }
                       }
                   },
                   responsive: [{
                       breakpoint: 480,
                       options: {
                           chart: { width: 200 },
                           legend: { position: 'bottom' }
                       }
                   }]
               };

               if (document.querySelector("#appointmentStatusChart")) {
                   this.charts.appointmentStatus = new ApexCharts(document.querySelector("#appointmentStatusChart"), appointmentStatusOptions);
                   this.charts.appointmentStatus.render();
               }
           },

           initTopDoctorsChart() {
               this.destroyChart('topDoctors');
               
               const topDoctorsOptions = {
                   series: [{
                       name: 'Consultations',
                       data: @json($topDoctorsData['data'])
                   }],
                   chart: {
                       type: 'bar',
                       height: 350,
                       toolbar: { show: false }
                   },
                   plotOptions: {
                       bar: {
                           borderRadius: 4,
                           horizontal: true,
                       }
                   },
                   dataLabels: { enabled: false },
                   xaxis: {
                       categories: @json($topDoctorsData['labels']),
                       labels: { style: { fontSize: '12px' } }
                   },
                   yaxis: {
                       labels: { style: { fontSize: '12px' } }
                   },
                   fill: { colors: ['#8B5CF6'] },
                   tooltip: {
                       y: {
                           formatter: function (val) {
                               return val + " consultations"
                           }
                       }
                   },
                   grid: {
                       borderColor: '#f3f4f6',
                       strokeDashArray: 3
                   }
               };

               if (document.querySelector("#topDoctorsChart")) {
                   this.charts.topDoctors = new ApexCharts(document.querySelector("#topDoctorsChart"), topDoctorsOptions);
                   this.charts.topDoctors.render();
               }
           },

           initPatientAgeChart() {
               this.destroyChart('patientAge');
               
               const patientAgeOptions = {
                   series: @json($patientAgeDistribution['data']),
                   chart: {
                       type: 'donut',
                       height: 350
                   },
                   labels: @json($patientAgeDistribution['labels']),
                   colors: ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7'],
                   legend: { position: 'bottom' },
                   tooltip: {
                       y: {
                           formatter: function (val) {
                               return val + " patients"
                           }
                       }
                   },
                   responsive: [{
                       breakpoint: 480,
                       options: {
                           chart: { width: 200 },
                           legend: { position: 'bottom' }
                       }
                   }]
               };

               if (document.querySelector("#patientAgeChart")) {
                   this.charts.patientAge = new ApexCharts(document.querySelector("#patientAgeChart"), patientAgeOptions);
                   this.charts.patientAge.render();
               }
           },

           initTimeSlotChart() {
               this.destroyChart('timeSlot');
               
               const timeSlotOptions = {
                   series: @json($appointmentsByTimeSlot['data']),
                   chart: {
                       type: 'pie',
                       height: 350
                   },
                   labels: @json($appointmentsByTimeSlot['labels']),
                   colors: ['#FFD93D', '#6BCF7F', '#4D96FF', '#9B59B6'],
                   legend: { position: 'bottom' },
                   tooltip: {
                       y: {
                           formatter: function (val) {
                               return val + " appointments"
                           }
                       }
                   }
               };

               if (document.querySelector("#timeSlotChart")) {
                   this.charts.timeSlot = new ApexCharts(document.querySelector("#timeSlotChart"), timeSlotOptions);
                   this.charts.timeSlot.render();
               }
           },

           initPatientGrowthChart() {
               this.destroyChart('patientGrowth');
               
               const patientGrowthOptions = {
                   series: [{
                       name: 'New Patients',
                       data: @json($monthlyPatientsGrowth['data'])
                   }],
                   chart: {
                       type: 'line',
                       height: 350,
                       toolbar: { show: false }
                   },
                   dataLabels: { enabled: false },
                   stroke: {
                       curve: 'smooth',
                       width: 3,
                       colors: ['#FF6B6B']
                   },
                   xaxis: {
                       categories: @json($monthlyPatientsGrowth['categories']),
                       labels: { style: { fontSize: '12px' } }
                   },
                   yaxis: {
                       title: { text: 'Number of Patients' }
                   },
                   tooltip: {
                       y: {
                           formatter: function (val) {
                               return val + " new patients"
                           }
                       }
                   },
                   grid: {
                       borderColor: '#f3f4f6',
                       strokeDashArray: 3
                   },
                   markers: {
                       size: 6,
                       colors: ['#FF6B6B'],
                       strokeColors: '#fff',
                       strokeWidth: 2,
                       hover: {
                           size: 8,
                       }
                   }
               };

               if (document.querySelector("#patientGrowthChart")) {
                   this.charts.patientGrowth = new ApexCharts(document.querySelector("#patientGrowthChart"), patientGrowthOptions);
                   this.charts.patientGrowth.render();
               }
           }
       }
   }
</script>
@endpush