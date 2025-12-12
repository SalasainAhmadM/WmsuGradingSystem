<div>
    <header class="navbar navbar-dark sticky-top flex-md-nowrap p-0" style="background-color: #952323;">
        <button class="d-flex bg-transparent" type="button" id="collapse_btn">
            <i class='bx bx-menu me-0 px-3 color-white fs-3' style="color: whitesmoke;"></i>
        </button>
        <nav class="navbar navbar-expand-md navbar-dark d-none d-md-block ">
            <div class="container-fluid">
                <div class="navbar-collapse offcanvas-collapse">
                    <ul class="navbar-nav me-auto d-flex align-items-center gap-2">
                        <li class="nav-item dropdown p-4">
                            <!-- <div class="dropdown">
                                <a role="button" class="nav-link dropdown-toggle m-1" id="navbarDropdown" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="bx bx-bell fs-4" style="color: whitesmoke;"></i>
                                    <span class="position-absolute top-20 end-25 translate-middle badge rounded-pill bg-primary">1</span>
                                </a>
                                <ul class="dropdown-menu text-small shadow z-1050 dropdown-menu-end custom-dropdown"
                                    style="right: 0 !important; left: auto !important;" aria-labelledby="navbarDropdown"
                                    onclick="event.stopPropagation()">
                                    <div class="d-flex flex-row justify-content-between align-items-center">
                                    <li class="fs-4 fw-bold px-3 text-start">Notifications</li>
                                    <a role="button" class="btn btn-primary px-2 mx-2 text-end" onclick="showClearAllModal()">Clear
                                        all</a>
                                    </div>
                                    <hr class="dropdown-divider">
                                        <li class="dropdown-item custom-item position-relative">
                                        <div style="border-bottom: solid grey 1px;">
                                            <p class="mb-0" style="padding-right:0.8em;">Notification message</p>
                                            <small class="text-muted"></small>
                                        </div>
                                        <button class="btn btn-sm btn ms-2 position-absolute" style="top:0; right: 0;"
                                            onclick="">
                                            <i class="bx bx-x"></i>
                                        </button>
                                        </li>
                                    <li class="dropdown-item text-muted">No notifications available.</li>
                                </ul>
                            </div> -->
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="modal fade" id="clearAllModal" tabindex="-1" aria-labelledby="clearAllModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearAllModalLabel">Clear All Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to clear all notifications? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmClearAll" onclick="deleteAllNotification()">Clear All</button>
            </div>
            </div>
        </div>
    </div>

    <script>
        $('#collapse_btn').click(()=>{
            var panel = $('.sidepanel');
            var currentWidth = panel.width();

            if (currentWidth === 280) {
                panel.animate({ width: '92px' }, 300);
            } else {
                panel.animate({ width: '280px' }, 300);
            }
        })
    </script>
</div>
