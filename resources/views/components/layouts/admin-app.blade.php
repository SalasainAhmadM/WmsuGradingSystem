<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
    <head>
        <style>
            #backToTop {
                z-index: 99;
                position: fixed;
                bottom: 20px;
                right: 20px;
                background-color: #2c0268;
                color: white;
                border: none;
                border-radius: 50%;
                padding: 10px;
                font-size: 18px;
                width: 50px;
                height: 50px;
                cursor: pointer;
                display: none;
            }
            #backToTop:hover {
                background-color: #00bad1;
            }
            /* Spinner container (optional) */
            .form-loader {
                text-align: center;
                font-weight: bold;
                font-size: 1.25rem;
            }

            /* Wave spinner */
            .sk-wave {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 4px;
            }

            .sk-wave-rect {
                background-color: #0d6efd; /* Bootstrap primary */
                height: 40px;
                width: 6px;
                animation: sk-wave 1.2s infinite ease-in-out;
            }

            .select2-results__option[aria-disabled="true"] {
                background-color: #f2f2f2;
                color: #999;
                cursor: not-allowed;
            }

            .sk-wave-rect:nth-child(1) { animation-delay: -1.2s; }
            .sk-wave-rect:nth-child(2) { animation-delay: -1.1s; }
            .sk-wave-rect:nth-child(3) { animation-delay: -1.0s; }
            .sk-wave-rect:nth-child(4) { animation-delay: -0.9s; }
            .sk-wave-rect:nth-child(5) { animation-delay: -0.8s; }

            @keyframes sk-wave {
                0%, 40%, 100% { transform: scaleY(0.4); }
                20% { transform: scaleY(1.0); }
            }

        </style>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Dashboard' }}@if(strtolower($title) != strtolower('Faculty')){{'s'}} @endif | GRADING SYSTEM </title>

        <link rel="icon" href="{{asset('image/wmsu_logo.webp')}}">
        <link rel="stylesheet" href="{{asset('css/style.css')}}">
        <script src="{{ asset('js/main.js') }}"></script>

        <!-- boxicon -->
        <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
         <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- bootstrap-5 -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <link rel="stylesheet" href="{{ asset('bootstrap/bootstrap-5.0.2/css/bootstrap.min.css')}}">
        <script src="{{ asset('bootstrap/bootstrap-5.0.2/js/bootstrap.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="{{ asset('jquery/jquery-3.7.1/jquery.min.js')}}" ></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
        <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        <!-- <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" /> -->

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- In your Blade layout file -->
        <link rel="stylesheet" href="{{ asset("assets/vendor/libs/flatpickr/flatpickr.css")}}" />
        <script src="{{ asset("assets/vendor/libs/flatpickr/flatpickr.js")}}"></script>

        <script src="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/dist/jquery-confirm.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/dist/jquery-confirm.min.css">

        <!-- <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script> -->


        @livewireStyles
        
    </head>
    <body>
        <div class="home">
            <div class="side">
                <livewire:Admin.SideNav.SideNav/>
            </div>
            <main>
            <div class="header">
                <livewire:Admin.Header.Header/>
            </div>
            {{ $slot }}
        </div>
        <button id="backToTop" class="back-to-top">
            <i class="ti ti-arrow-up "></i>
        </button>
        <script>
            let backToTopButton = document.getElementById("backToTop");
            window.onscroll = function() {
                if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                    backToTopButton.style.display = "block";
                } else {
                    backToTopButton.style.display = "none";
                }
            };
            backToTopButton.onclick = function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            };
        </script>
        @livewireScripts
        @stack('footer-scripts')

        <script>
            Livewire.on('notifySuccess', message => {
                const notyf = new Notyf({
                    position: {
                        x: 'center',
                        y: 'center',
                    },
                    types: [
                        {
                            type: 'success',
                            background: 'green',
                            icon: false
                        },
                        {
                            type: 'success',
                            background: 'green',
                            icon: false
                        }
                    ]
                });
                notyf.success(message[0]);
                if (message[1]) {
                    setTimeout(() => {
                        window.location.href = message[1];
                    }, 1500); // delay in milliseconds, e.g. 2000 = 2 seconds
                }
            });
            Livewire.on('notifyWarning', message => {
                const notyf = new Notyf({
                    position: {
                        x: 'center',
                        y: 'center',
                    },
                    types: [
                        {
                            type: 'success',
                            background: 'red',
                            icon: false
                        },
                        {
                            type: 'success',
                            background: 'red',
                            icon: false
                        }
                    ]
                });
                notyf.success(message[0]);
                if (message[1]) {
                    setTimeout(() => {
                        window.location.href = message[1];
                    }, 1500); // delay in milliseconds, e.g. 2000 = 2 seconds
                }
            });
            @if (request()->is('admin/schedule*')) 
                document.addEventListener('DOMContentLoaded', function () {
                    setTimeout(()=>{
                        $('#day').trigger('change');
                    },100)
                });
            @endif
            @if (request()->is('admin/subjects*')) 
                document.addEventListener('DOMContentLoaded', function () {
                    setTimeout(()=>{
                        $('#prerequisite_subject_id').trigger('change');
                    },100)
                });
            @endif
            Livewire.on('navigateTo', ({ url }) => {
                Livewire.navigate(url);
            });
            Livewire.on('openModal', ({ modal_id }) => {
                var myModal = new bootstrap.Modal(document.getElementById(modal_id));
                myModal.show();
            }); 
            Livewire.on('closeModal', ({ modal_id }) => {
                var myModal = document.getElementById(modal_id+'close');
                myModal.click();
            }); 

              Livewire.on('resetStudentLists', () => {
                setTimeout(()=>{
                    $('#student_lists').select2();
                },1000)
            }); 


            Livewire.on('openAttendanceModal', (obj) => {
                var res = obj[0].obj;
                var schedule_id = res.schedule_id;
                var term_id = res.term_id;
                $('#attendanceModal').on('shown.bs.modal', function () {
                    renderCalendar(res);
                });
            });

            const renderCalendar = (res) =>{
                var calendarEl = document.getElementById('flatpickr-calendar');
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    initialDate: new Date(),
                    height: 400,
                    events: {
                        url: '/api/attendance-dates', // Replace with your actual backend route
                        method: 'GET',
                        extraParams: {
                            schedule_id: res.schedule_id,  // Replace with dynamic ID if needed
                            term_id: res.term_id         // Replace with dynamic ID if needed
                        },
                        failure: function() {
                            alert('There was an error while fetching events!');
                        },
                        color: 'green',   // Optional: set default color
                        textColor: 'white'
                    },
                    eventSourceSuccess: function(content, response) {   
                        setTimeout(() => {
                            const allDayCells = document.querySelectorAll('.fc-daygrid-day');
                            // Get all event dates in YYYY-MM-DD format
                            const eventDates = new Map(
                                calendar.getEvents().map(e => {
                                    const date = new Date(e.start);
                                    const dateStr = date.toLocaleDateString('en-CA'); // 'YYYY-MM-DD'
                                    return [dateStr, e.id]; // assuming each event has an `id`
                                })
                            );



                            allDayCells.forEach(cell => {
                                const dateStr = cell.getAttribute('data-date');
                                const frame = cell.querySelector('.fc-daygrid-day-frame');
                                // Add data attribute based on event presence
                                if (eventDates.has(dateStr)) {
                                    const eventId = eventDates.get(dateStr);
                                    frame.querySelectorAll('.fc-custom-add-btn').forEach(btn => btn.remove());
                                    frame.style.color = 'white';
                                    const btn = document.createElement('button');
                                    btn.innerText = '-';
                                    btn.classList.add('fc-custom-add-btn');
                                    btn.style.fontSize = '10px';
                                    btn.style.marginTop = '2px';
                                    btn.style.background = '#dc2626';
                                    btn.style.color = 'white';
                                    btn.style.border = 'none';
                                    btn.style.borderRadius = '3px';
                                    btn.style.padding = '1px 10px';
                                    btn.type = 'button';
                                    btn.style.cursor = 'pointer'; 
                                    btn.style.position = 'relative'; 
                                    btn.style.zIndex = '9999';  
                                    btn.onclick = () => {
                                        const payload = {
                                            schedule_id: res.schedule_id,
                                            term_id: res.term_id,
                                            date: dateStr,
                                            id: eventId  // make sure you already got this from the map
                                        };

                                        fetch('/api/attendance-dates/remove', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Laravel CSRF
                                            },
                                            body: JSON.stringify(payload)
                                        })
                                        .then(response => {
                                            if (!response.ok) throw new Error('Request failed');
                                            return response.json();
                                        })
                                        .then(data => {
                                            renderCalendar({
                                                schedule_id: res.schedule_id,
                                                term_id: res.term_id,
                                            })
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('Something went wrong!');
                                        });
                                    };

                                    frame.appendChild(btn);

                                    const btn2 = document.createElement('button');

                                    btn2.innerText = '👁';
                                    btn2.classList.add('fc-custom-add-btn');
                                    btn2.style.fontSize = '10px';
                                    btn2.style.marginTop = '2px';
                                    btn2.style.marginLeft = '15px';
                                    btn2.style.background = '#647068';
                                    btn2.style.color = 'white';
                                    btn2.style.border = 'none';
                                    btn2.style.borderRadius = '3px';
                                    btn2.style.padding = '1px 10px';
                                    btn2.style.cursor = 'pointer';
                                    btn2.type = 'button';
                                    btn2.style.position = 'relative'; 
                                    btn2.style.zIndex = '9999';  

                                    btn2.onclick = () => {
                                        const formattedDate = formatDateMMDDYY(dateStr);
                                        const url = `/admin/faculty-and-scheduling/evaluation-${res.schedule_id}/attendance-${eventId}`;
                                        window.open(url, '_blank');
                                    };
                                    frame.appendChild(btn2);
                                } else {
                                    frame.querySelectorAll('.fc-custom-add-btn').forEach(btn => btn.remove());
                                    // ➕ Add button for empty dates
                                    const btn = document.createElement('button');
                                    btn.innerText = '+';
                                    btn.classList.add('fc-custom-add-btn');
                                    btn.style.fontSize = '10px';
                                    btn.style.marginTop = '2px';
                                    btn.style.background = '#20753b';
                                    btn.style.color = 'white';
                                    btn.style.border = 'none';
                                    btn.style.borderRadius = '3px';
                                    btn.style.padding = '1px 10px';
                                    btn.style.cursor = 'pointer';
                                    btn.type = 'button';
                                    btn.style.cursor = 'pointer'; 

                                    btn.onclick = () => {
                                            const payload = {
                                            schedule_id: res.schedule_id,
                                            term_id: res.term_id,
                                            date: dateStr,
                                        };

                                        fetch('/api/attendance-dates/add', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Laravel CSRF
                                            },
                                            body: JSON.stringify(payload)
                                        })
                                        .then(response => {
                                            if (!response.ok) throw new Error('Request failed');
                                            return response.json();
                                        })
                                        .then(data => {
                                            renderCalendar({
                                                schedule_id: res.schedule_id,
                                                term_id: res.term_id,
                                            })
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('Something went wrong!');
                                        });
                                    };

                                    frame.appendChild(btn);
                                }
                            });
                        }, 0);
                    }
                });
                calendar.render();
            }

            function formatDateMMDDYY(dateStr) {
                const [year, month, day] = dateStr.split('-');
                return `${month}.${day}.${year.slice(-2)}`;
            }

            Livewire.on('openFacultyAttendanceModal', (obj) => {
                var res = obj[0].obj;
                var schedule_id = res.schedule_id;
                var school_year = res.school_year;
                var semester = res.semester;
                var term_id = res.term_id;
                $('#attendanceModal').on('shown.bs.modal', function () {
                    renderFacultyCalendar(res);
                });
            });

            const renderFacultyCalendar = (res) =>{
                var calendarEl = document.getElementById('flatpickr-calendar');
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    initialDate: new Date(),
                    height: 400,
                    events: {
                        url: '/api/attendance-dates', // Replace with your actual backend route
                        method: 'GET',
                        extraParams: {
                            schedule_id: res.schedule_id,  // Replace with dynamic ID if needed
                            term_id: res.term_id         // Replace with dynamic ID if needed
                        },
                        failure: function() {
                            alert('There was an error while fetching events!');
                        },
                        color: 'green',   // Optional: set default color
                        textColor: 'white'
                    },
                    eventSourceSuccess: function(content, response) {   
                        setTimeout(() => {
                            const allDayCells = document.querySelectorAll('.fc-daygrid-day');
                            // Get all event dates in YYYY-MM-DD format
                            const eventDates = new Map(
                                calendar.getEvents().map(e => {
                                    const date = new Date(e.start);
                                    const dateStr = date.toLocaleDateString('en-CA'); // 'YYYY-MM-DD'
                                    return [dateStr, e.id]; // assuming each event has an `id`
                                })
                            );



                            allDayCells.forEach(cell => {
                                const dateStr = cell.getAttribute('data-date');
                                const frame = cell.querySelector('.fc-daygrid-day-frame');
                                // Add data attribute based on event presence
                                if (eventDates.has(dateStr)) {
                                    const eventId = eventDates.get(dateStr);
                                    frame.querySelectorAll('.fc-custom-add-btn').forEach(btn => btn.remove());
                                    frame.style.color = 'white';
                                    const btn = document.createElement('button');
                                    btn.innerText = '-';
                                    btn.classList.add('fc-custom-add-btn');
                                    btn.style.fontSize = '10px';
                                    btn.style.marginTop = '2px';
                                    btn.style.background = '#dc2626';
                                    btn.style.color = 'white';
                                    btn.style.border = 'none';
                                    btn.style.borderRadius = '3px';
                                    btn.style.padding = '1px 10px';
                                    btn.type = 'button';
                                    btn.style.cursor = 'pointer'; 
                                    btn.style.position = 'relative'; 
                                    btn.style.zIndex = '9999';  
                                    btn.onclick = () => {
                                        const payload = {
                                            schedule_id: res.schedule_id,
                                            term_id: res.term_id,
                                            date: dateStr,
                                            id: eventId  // make sure you already got this from the map
                                        };

                                        fetch('/api/attendance-dates/remove', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Laravel CSRF
                                            },
                                            body: JSON.stringify(payload)
                                        })
                                        .then(response => {
                                            if (!response.ok) throw new Error('Request failed');
                                            return response.json();
                                        })
                                        .then(data => {
                                            renderCalendar({
                                                schedule_id: res.schedule_id,
                                                term_id: res.term_id,
                                            })
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('Something went wrong!');
                                        });
                                    };

                                    frame.appendChild(btn);

                                    const btn2 = document.createElement('button');

                                    btn2.innerText = '👁';
                                    btn2.classList.add('fc-custom-add-btn');
                                    btn2.style.fontSize = '10px';
                                    btn2.style.marginTop = '2px';
                                    btn2.style.marginLeft = '15px';
                                    btn2.style.background = '#647068';
                                    btn2.style.color = 'white';
                                    btn2.style.border = 'none';
                                    btn2.style.borderRadius = '3px';
                                    btn2.style.padding = '1px 10px';
                                    btn2.style.cursor = 'pointer';
                                    btn2.type = 'button';
                                    btn2.style.position = 'relative'; 
                                    btn2.style.zIndex = '9999';  

                                    btn2.onclick = () => {
                                        const formattedDate = formatDateMMDDYY(dateStr);
                                        const url = `/faculty/my-schedules/${res.school_year}/${res.semester}/evaluation-${res.schedule_id}/attendance-${eventId}`;
                                        window.open(url, '_blank');
                                    };
                                    frame.appendChild(btn2);
                                } else {
                                    frame.querySelectorAll('.fc-custom-add-btn').forEach(btn => btn.remove());
                                    // ➕ Add button for empty dates
                                    const btn = document.createElement('button');
                                    btn.innerText = '+';
                                    btn.classList.add('fc-custom-add-btn');
                                    btn.style.fontSize = '10px';
                                    btn.style.marginTop = '2px';
                                    btn.style.background = '#20753b';
                                    btn.style.color = 'white';
                                    btn.style.border = 'none';
                                    btn.style.borderRadius = '3px';
                                    btn.style.padding = '1px 10px';
                                    btn.style.cursor = 'pointer';
                                    btn.type = 'button';
                                    btn.style.cursor = 'pointer'; 

                                    btn.onclick = () => {
                                            const payload = {
                                            schedule_id: res.schedule_id,
                                            term_id: res.term_id,
                                            date: dateStr,
                                        };

                                        fetch('/api/attendance-dates/add', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Laravel CSRF
                                            },
                                            body: JSON.stringify(payload)
                                        })
                                        .then(response => {
                                            if (!response.ok) throw new Error('Request failed');
                                            return response.json();
                                        })
                                        .then(data => {
                                            renderCalendar({
                                                schedule_id: res.schedule_id,
                                                term_id: res.term_id,
                                            })
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('Something went wrong!');
                                        });
                                    };

                                    frame.appendChild(btn);
                                }
                            });
                        }, 0);
                    }
                });
                calendar.render();
            }

            function togglePassword() {
                const input = document.getElementById("password");
                const icon = document.getElementById("eye-icon");
                const isHidden = input.type === "password";

                input.type = isHidden ? "text" : "password";
                icon.classList.toggle("fa-eye");
                icon.classList.toggle("fa-eye-slash");
            }

            
        </script>
       

        <!-- <script>
            function initSelect2() {
                $('#schedule_id').select2({
                    placeholder: 'Select Subject',  
                    allowClear: true,
                    width: '100%',
                });
                // Update Livewire when select2 changes
                    $('#schedule_id').on('change', function () {
                    let component = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
                    component.set('detail.schedule_id', $(this).val());
                    component.call('selectSubject');
                });
            }

            Livewire.on('select2', () => {
                initSelect2();
            }); 
            Livewire.hook('message.processed', (message, component) => {
                initSelect2();
            });
        </script> -->

        
    </body>
</html>
