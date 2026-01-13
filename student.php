<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- GLOBAL STYLES --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #ffffff; color: #333; min-height: 100vh; display: flex; flex-direction: column; }

        /* --- HEADER --- */
        header { height: 65px; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e0e0e0; background: white; position: fixed; top: 0; width: 100%; z-index: 1000; }
        .logo-area { display: flex; align-items: center; gap: 12px; }
        .logo-icon { font-size: 28px; color: #1a73e8; }
        .logo-text { font-size: 22px; font-weight: 600; color: #202124; }
        .profile-pic { width: 35px; height: 35px; border-radius: 50%; background-image: url('https://i.pravatar.cc/150?img=12'); background-size: cover; cursor: pointer; }

        /* --- LAYOUT --- */
        .app-container { display: flex; margin-top: 65px; height: calc(100vh - 65px); }
        .sidebar { width: 280px; padding: 24px 0; flex-shrink: 0; overflow-y: auto; border-right: 1px solid transparent; }
        .main-content { flex-grow: 1; padding: 24px; overflow-y: auto; }

        /* --- SIDEBAR ITEMS --- */
        .sidebar-item { display: flex; align-items: center; padding: 12px 24px; font-size: 16px; font-weight: 500; color: #3c4043; cursor: pointer; border-radius: 0 24px 24px 0; margin-bottom: 4px; transition: background 0.2s; text-decoration: none; }
        .sidebar-item:hover { background-color: #f5f5f5; }
        .sidebar-item.active { background-color: #e8f0fe; color: #1967d2; }
        .sidebar-item i { margin-right: 15px; width: 24px; text-align: center; }
        
        /* Dropdown Styles */
        .dropdown-toggle { justify-content: space-between; }
        .toggle-content { display: flex; align-items: center; gap: 18px; }
        .toggle-content i { margin-right: 0; }
        .chevron-icon { font-size: 12px !important; transition: transform 0.3s ease; margin-left: auto; width: auto !important; }
        .submenu-container { display: block; overflow: hidden; }
        .sub-item { padding-left: 64px; font-size: 14px; }
        .sub-item.active-sub { color: #1967d2; background-color: #e8f0fe; font-weight: 600; }

        /* --- CARD GRID --- */
        .card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; }
        
        .class-card { background: white; border: 1px solid #dadce0; border-radius: 8px; overflow: visible; display: flex; flex-direction: column; position: relative; height: 280px; transition: box-shadow 0.2s; cursor: pointer; }
        .class-card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        
        /* Card Colors */
        .card-header-bg { height: 100px; padding: 16px; color: white; position: relative; border-top-left-radius: 8px; border-top-right-radius: 8px; }
        .bg-orange { background: linear-gradient(135deg, #ff7e5f, #feb47b); }
        .bg-gray { background: linear-gradient(135deg, #bdc3c7, #2c3e50); }
        .bg-green { background: linear-gradient(135deg, #11998e, #38ef7d); }
        
        .class-title { font-size: 20px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .class-subtitle { font-size: 14px; margin-top: 4px; opacity: 0.9; }
        .teacher-name { font-size: 12px; margin-top: 4px; opacity: 0.8; }
        
        /* Avatar */
        .card-avatar { width: 70px; height: 70px; border-radius: 50%; position: absolute; top: 65px; right: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: white; border: 2px solid white; }
        .avatar-purple { background-color: #4a148c; }
        .avatar-photo { background-image: url('https://i.pravatar.cc/150?img=5'); background-size: cover; }
        
        .card-body { flex-grow: 1; padding: 16px; }
        .card-footer { height: 50px; border-top: 1px solid #dadce0; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px; gap: 10px; }
        
        /* Card Menu (3 dots) */
        .menu-wrapper { position: relative; }
        .card-menu { display: none; position: absolute; bottom: 40px; right: 0; background-color: white; min-width: 160px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); border-radius: 4px; padding: 8px 0; z-index: 100; }
        .card-menu.show { display: block; }
        .menu-item { display: block; padding: 10px 20px; color: #333; text-decoration: none; font-size: 14px; cursor: pointer; }
        .menu-item:hover { background-color: #f1f3f4; }
        .footer-icon { color: #5f6368; padding: 8px; border-radius: 50%; cursor: pointer; }
        .footer-icon:hover { background-color: #eee; }

        /* --- STREAM & CLASSWORK STYLES --- */
        .content-container { max-width: 1000px; margin: 0 auto; }
        .class-nav-tabs { display: flex; border-bottom: 1px solid #e0e0e0; margin-bottom: 20px; }
        .nav-tab { padding: 16px 24px; text-decoration: none; color: #5f6368; font-weight: 500; border-bottom: 4px solid transparent; cursor: pointer; }
        .nav-tab:hover { background-color: #f5f5f5; color: #202124; }
        .nav-tab.active-tab { color: #1967d2; border-bottom-color: #1967d2; }
        
        .banner { height: 240px; background-size: cover; background-position: center; border-radius: 8px; padding: 24px; display: flex; flex-direction: column; justify-content: flex-end; color: white; margin-bottom: 24px; }
        .banner h1 { font-size: 32px; font-weight: 600; }
        
        .stream-layout { display: grid; grid-template-columns: 200px 1fr; gap: 24px; }
        .upcoming-box { border: 1px solid #dadce0; border-radius: 8px; padding: 16px; background: white; height: fit-content; }
        .stream-item { border: 1px solid #dadce0; border-radius: 8px; background: white; padding: 16px; display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
        .item-icon { width: 36px; height: 36px; border-radius: 50%; background-color: #1967d2; color: white; display: flex; align-items: center; justify-content: center; }

        /* Classwork Specific */
        .cw-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px; }
        .view-work-btn { border: 1px solid #dadce0; background: white; padding: 8px 16px; border-radius: 20px; color: #1967d2; font-weight: 500; text-decoration: none; display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .view-work-btn:hover { background-color: #f0f8ff; border-color: #d2e3fc; }
        .cw-topic-title { font-size: 24px; color: #1967d2; font-weight: 400; margin-top: 30px; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #1967d2; display: flex; justify-content: space-between; align-items: center; }
        .cw-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-bottom: 1px solid #e0e0e0; transition: background 0.2s; cursor: pointer; }
        .cw-item:hover { background-color: #f0f4f8; }
        .cw-left { display: flex; align-items: center; gap: 16px; }
        .cw-icon-circle { width: 36px; height: 36px; border-radius: 50%; background-color: #ddd; display: flex; align-items: center; justify-content: center; color: white; }
        .cw-icon-circle.material { background-color: #bcbcbc; }
        .cw-icon-circle.assignment { background-color: #1967d2; }
        .cw-title { font-size: 14px; font-weight: 500; color: #3c4043; }
        .cw-date { font-size: 12px; color: #5f6368; margin-right: 16px; }

        /* --- PEOPLE TAB STYLES --- */
        .people-section-header { font-size: 32px; color: #1967d2; margin-bottom: 16px; margin-top: 24px; padding-bottom: 16px; border-bottom: 1px solid #1967d2; }
        .people-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border-bottom: 1px solid #e0e0e0; }
        .people-info { display: flex; align-items: center; gap: 16px; }
        .people-name { font-size: 14px; font-weight: 500; color: #3c4043; }
        .people-avatar { width: 32px; height: 32px; border-radius: 50%; background-color: #1967d2; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; }

    </style>
</head>
<body>

    <header>
        <div class="logo-area">
            <i class="fa-solid fa-bars" style="margin-right:15px; font-size: 20px; color: #5f6368; cursor: pointer;"></i>
            <i class="fa-solid fa-book-open logo-icon"></i>
            <span class="logo-text">TechHub</span>
        </div>
        <div class="profile-pic"></div>
    </header>

    <div class="app-container">
        
        <aside class="sidebar">
            <div id="nav-home" class="sidebar-item active" onclick="navigate('home')">
                <i class="fa-solid fa-house"></i> Home
            </div>

            <div class="sidebar-item dropdown-toggle" onclick="toggleEnrolled()">
                <div class="toggle-content">
                    <i class="fa-solid fa-graduation-cap"></i> Enrolled
                </div>
                <i class="fa-solid fa-chevron-down chevron-icon" id="enrolled-chevron"></i>
            </div>

            <div class="submenu-container" id="enrolled-submenu">
                </div>

            <div id="nav-unenrolled" class="sidebar-item" onclick="navigate('unenrolled')">
                <i class="fa-solid fa-box-archive"></i> Unenroll Classes
            </div>
        </aside>

        <main class="main-content" id="main-content">
            </main>
    </div>

    <script>
        // --- 1. DATA: Replaces the PHP Array ---
        // Added 'people' property to hold student lists
        const classes = {
            'art': {
                id: 'class-art',
                name: 'Art Appreciation',
                subtitle: 'BSIT 3-1',
                teacher: 'Archie Arevalo',
                color: 'bg-gray',
                avatar_color: 'avatar-photo',
                avatar_text: '',
                banner: 'https://gstatic.com/classroom/themes/img_read.jpg',
                posts: [
                    {author: 'Archie Arevalo', action: 'posted a new material: Lecture 3 - Module', date: 'Yesterday', icon: 'fa-book-bookmark'},
                    {author: 'Archie Arevalo', action: 'posted a new assignment: Group Presentation', date: 'Dec 4, 2025', icon: 'fa-clipboard-list'}
                ],
                classwork: {
                    'Course Outline': [
                        {title: 'Course Syllabus - BSIT - Art Appreciation', type: 'material', date: 'Edited Dec 4, 2025', icon: 'fa-book-bookmark'}
                    ],
                    'Lecture 1 - Art and its Forms': [
                        {title: 'Lesson 1 - Art and its Forms', type: 'material', date: 'Posted Dec 4, 2025', icon: 'fa-book-bookmark'},
                        {title: 'Lecture 1 - Video Lectures', type: 'material', date: 'Edited Dec 4, 2025', icon: 'fa-book-bookmark'},
                        {title: 'Quiz no. 1 - Art Forms', type: 'assignment', date: 'Due Dec 11, 2025', icon: 'fa-clipboard-list'}
                    ]
                },
                // NEW: People Data
                people: {
                    teachers: ['Archie Arevalo'],
                    students: [
                        'Abdulmalik, Student', 'Cruz, Juan', 'Dela Cruz, Maria', 'Santos, Jose', 'Reyes, Ana', 
                        'Garcia, Miguel', 'Lopez, Sofia', 'Tan, Kevin', 'Lim, Christine', 'Gonzales, Mark'
                    ]
                }
            },
            'web': {
                id: 'class-web',
                name: 'Web Development',
                subtitle: 'BSIT 3-1',
                teacher: 'Prof. Jhomari',
                color: 'bg-green',
                avatar_color: 'avatar-purple',
                avatar_text: 'Ed',
                banner: 'https://gstatic.com/classroom/themes/img_code.jpg',
                posts: [
                    {author: 'Prof. Jhomari', action: 'posted a new material: Github Setup', date: '10:45 AM', icon: 'fa-code'}
                ],
                classwork: {
                    'HTML & CSS Basics': [
                        {title: 'Activity 1: Create a Portfolio', type: 'assignment', date: 'Due Tomorrow', icon: 'fa-clipboard-list'}
                    ]
                },
                people: {
                    teachers: ['Prof. Jhomari'],
                    students: [
                        'Abdulmalik, Student', 'Bautista, Sarah', 'Ocampo, Luis', 'Villanueva, Bea', 'Castillo, Ryan',
                        'Mendoza, John', 'Salazar, Kim', 'Torres, David'
                    ]
                }
            }
        };

        // --- 2. STATE MANAGEMENT ---
        let currentPage = 'home'; 
        let currentSubjectId = null; 
        let currentTab = 'stream'; // stream, classwork, or people

        // --- 3. INIT ---
        document.addEventListener('DOMContentLoaded', () => {
            renderSidebar();
            navigate('home');
            
            // Sidebar toggle defaults
            document.getElementById('enrolled-submenu').style.display = "block";
            document.getElementById('enrolled-chevron').style.transform = "rotate(-180deg)";
        });

        // --- 4. NAVIGATION FUNCTIONS ---
        function navigate(page, subjectId = null) {
            currentPage = page;
            currentSubjectId = subjectId;
            currentTab = 'stream'; // Reset tab when switching classes

            updateSidebarActiveState();
            
            const container = document.getElementById('main-content');
            container.innerHTML = ''; // Clear content

            if (page === 'home') {
                renderHome(container);
            } else if (page === 'unenrolled') {
                renderUnenrolled(container);
            } else if (page === 'class') {
                renderClassView(container, subjectId);
            }
        }

        function switchClassTab(tabName) {
            currentTab = tabName;
            const container = document.getElementById('main-content');
            container.innerHTML = '';
            renderClassView(container, currentSubjectId);
        }

        function updateSidebarActiveState() {
            // Reset all
            document.querySelectorAll('.sidebar-item').forEach(el => el.classList.remove('active', 'active-sub'));
            document.getElementById('nav-home').classList.remove('active');
            document.getElementById('nav-unenrolled').classList.remove('active');

            if (currentPage === 'home') {
                document.getElementById('nav-home').classList.add('active');
            } else if (currentPage === 'unenrolled') {
                document.getElementById('nav-unenrolled').classList.add('active');
            } else if (currentPage === 'class') {
                const link = document.getElementById(`link-${currentSubjectId}`);
                if (link) link.classList.add('active-sub');
            }
        }

        // --- 5. RENDER FUNCTIONS ---
        
        function renderSidebar() {
            const submenu = document.getElementById('enrolled-submenu');
            submenu.innerHTML = '';
            
            for (const [key, cls] of Object.entries(classes)) {
                if (localStorage.getItem(cls.id) === 'unenrolled') continue;

                const div = document.createElement('div');
                div.id = `link-${key}`;
                div.className = 'sidebar-item sub-item';
                div.innerText = cls.name;
                div.onclick = () => navigate('class', key);
                submenu.appendChild(div);
            }
        }

        function renderHome(container) {
            const grid = document.createElement('div');
            grid.className = 'card-grid';
            
            for (const [key, cls] of Object.entries(classes)) {
                if (localStorage.getItem(cls.id) === 'unenrolled') continue;

                const card = document.createElement('div');
                card.className = 'class-card';
                card.onclick = () => navigate('class', key);
                
                card.innerHTML = `
                    <div class="card-header-bg ${cls.color}">
                        <div class="class-title">${cls.name}</div>
                        <div class="class-subtitle">${cls.subtitle}</div>
                        <div class="teacher-name">${cls.teacher}</div>
                    </div>
                    <div class="card-avatar ${cls.avatar_color}">${cls.avatar_text}</div>
                    <div class="card-body"></div>
                    <div class="card-footer">
                        <i class="fa-regular fa-folder footer-icon"></i>
                        <div class="menu-wrapper">
                            <i class="fa-solid fa-ellipsis-vertical footer-icon menu-btn" onclick="toggleMenu(event, this)"></i>
                            <div class="card-menu" onclick="event.stopPropagation()">
                                <div class="menu-item">Move</div>
                                <div class="menu-item" onclick="unenrollClass('${cls.id}')">Unenroll</div>
                            </div>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            }
            container.appendChild(grid);
        }

        function renderUnenrolled(container) {
            const h2 = document.createElement('h2');
            h2.style.marginBottom = '20px'; h2.style.fontWeight = '400'; h2.style.color = '#5f6368';
            h2.innerText = 'Unenrolled Classes';
            container.appendChild(h2);

            const grid = document.createElement('div');
            grid.className = 'card-grid';
            let hasItems = false;

            for (const [key, cls] of Object.entries(classes)) {
                if (localStorage.getItem(cls.id) !== 'unenrolled') continue;
                hasItems = true;

                const card = document.createElement('div');
                card.className = 'class-card';
                card.style.cursor = 'default';
                
                card.innerHTML = `
                    <div class="card-header-bg ${cls.color}" style="filter: grayscale(1);">
                        <div class="class-title">${cls.name}</div>
                        <div class="class-subtitle">${cls.subtitle}</div>
                    </div>
                    <div class="card-body" style="display:flex; align-items:center; justify-content:center; color:#777;">Unenrolled</div>
                    <div class="card-footer">
                        <button onclick="restoreClass('${cls.id}')" style="border:none; background:none; color:#1a73e8; cursor:pointer; font-weight:600;">RESTORE</button>
                    </div>
                `;
                grid.appendChild(card);
            }

            if (!hasItems) {
                const empty = document.createElement('div');
                empty.style.textAlign = 'center'; empty.style.marginTop = '50px'; empty.style.color = '#999';
                empty.innerText = 'No unenrolled classes.';
                container.appendChild(empty);
            } else {
                container.appendChild(grid);
            }
        }

        function renderClassView(container, subjectId) {
            const cls = classes[subjectId];
            if(!cls) return;

            const contentDiv = document.createElement('div');
            contentDiv.className = 'content-container';

            // Navigation Tabs
            contentDiv.innerHTML = `
                <nav class="class-nav-tabs">
                    <div class="nav-tab ${currentTab === 'stream' ? 'active-tab' : ''}" onclick="switchClassTab('stream')">Stream</div>
                    <div class="nav-tab ${currentTab === 'classwork' ? 'active-tab' : ''}" onclick="switchClassTab('classwork')">Classwork</div>
                    <div class="nav-tab ${currentTab === 'people' ? 'active-tab' : ''}" onclick="switchClassTab('people')">People</div>
                </nav>
            `;

            if (currentTab === 'stream') {
                // RENDER STREAM
                let postsHtml = '';
                cls.posts.forEach(post => {
                    postsHtml += `
                        <div class="stream-item">
                            <div class="item-icon"><i class="fa-solid ${post.icon}"></i></div>
                            <div>
                                <h4 style="font-size:14px; font-weight:500; color:#3c4043;">${post.author} ${post.action}</h4>
                                <div style="font-size:12px; color:#5f6368;">${post.date}</div>
                            </div>
                        </div>
                    `;
                });

                const streamHtml = `
                    <div class="banner" style="background-image: url('${cls.banner}');">
                        <h1>${cls.name}</h1>
                        <p>${cls.subtitle}</p>
                    </div>
                    <div class="stream-layout">
                        <div class="upcoming-box">
                            <h4>Upcoming</h4><p style="font-size:12px; color:#5f6368;">No work due soon!</p>
                        </div>
                        <div class="stream-feed">
                            <div class="stream-item" style="box-shadow: 0 1px 2px rgba(0,0,0,0.1); cursor: text;">
                                <div class="profile-pic"></div>
                                <span style="font-size:13px; color:#5f6368;">Announce something to your class</span>
                            </div>
                            ${postsHtml}
                        </div>
                    </div>
                `;
                const div = document.createElement('div');
                div.innerHTML = streamHtml;
                contentDiv.appendChild(div);

            } else if (currentTab === 'classwork') {
                // RENDER CLASSWORK
                const header = document.createElement('div');
                header.className = 'cw-header-row';
                header.innerHTML = `
                    <div style="font-size: 14px; color: #1a73e8;">View all topics</div>
                    <a href="#" class="view-work-btn"><i class="fa-regular fa-user"></i> View your work</a>
                `;
                contentDiv.appendChild(header);

                if (cls.classwork) {
                    for (const [topic, items] of Object.entries(cls.classwork)) {
                        const topicTitle = document.createElement('div');
                        topicTitle.className = 'cw-topic-title';
                        topicTitle.innerHTML = `${topic} <i class="fa-solid fa-ellipsis-vertical" style="font-size: 16px; color: #5f6368; cursor: pointer;"></i>`;
                        contentDiv.appendChild(topicTitle);

                        items.forEach(item => {
                            const bgClass = (item.type === 'assignment') ? 'assignment' : 'material';
                            const itemDiv = document.createElement('div');
                            itemDiv.className = 'cw-item';
                            itemDiv.innerHTML = `
                                <div class="cw-left">
                                    <div class="cw-icon-circle ${bgClass}">
                                        <i class="fa-solid ${item.icon}"></i>
                                    </div>
                                    <div class="cw-title">${item.title}</div>
                                </div>
                                <div style="display: flex; align-items: center;">
                                    <div class="cw-date">${item.date}</div>
                                    <i class="fa-solid fa-ellipsis-vertical cw-menu"></i>
                                </div>
                            `;
                            contentDiv.appendChild(itemDiv);
                        });
                    }
                }
            } else if (currentTab === 'people') {
                // RENDER PEOPLE TAB
                const teachersHeader = document.createElement('div');
                teachersHeader.className = 'people-section-header';
                teachersHeader.innerText = 'Teachers';
                contentDiv.appendChild(teachersHeader);

                cls.people.teachers.forEach(teacher => {
                    const row = document.createElement('div');
                    row.className = 'people-row';
                    row.innerHTML = `
                        <div class="people-info">
                            <div class="people-avatar"><i class="fa-solid fa-user"></i></div>
                            <div class="people-name">${teacher}</div>
                        </div>
                        <i class="fa-regular fa-envelope" style="color:#5f6368; cursor:pointer;"></i>
                    `;
                    contentDiv.appendChild(row);
                });

                const studentsHeader = document.createElement('div');
                studentsHeader.className = 'people-section-header';
                studentsHeader.style.display = 'flex';
                studentsHeader.style.justifyContent = 'space-between';
                studentsHeader.innerHTML = `Classmates <span style="font-size:14px; color:#1967d2; font-weight:500;">${cls.people.students.length} students</span>`;
                contentDiv.appendChild(studentsHeader);

                cls.people.students.forEach(student => {
                    const row = document.createElement('div');
                    row.className = 'people-row';
                    row.innerHTML = `
                        <div class="people-info">
                            <div class="people-avatar"><i class="fa-solid fa-user"></i></div>
                            <div class="people-name">${student}</div>
                        </div>
                        <i class="fa-regular fa-envelope" style="color:#5f6368; cursor:pointer;"></i>
                    `;
                    contentDiv.appendChild(row);
                });
            }

            container.appendChild(contentDiv);
        }

        // --- 6. UTILITIES ---
        function toggleEnrolled() {
            const submenu = document.getElementById('enrolled-submenu');
            const chevron = document.getElementById('enrolled-chevron');
            if (submenu.style.display === "none") {
                submenu.style.display = "block";
                chevron.style.transform = "rotate(-180deg)";
            } else {
                submenu.style.display = "none";
                chevron.style.transform = "rotate(0deg)";
            }
        }

        function toggleMenu(e, btn) {
            e.stopPropagation();
            const dropdown = btn.nextElementSibling;
            document.querySelectorAll('.card-menu').forEach(menu => {
                if (menu !== dropdown) menu.classList.remove('show');
            });
            dropdown.classList.toggle('show');
        }

        window.addEventListener('click', () => {
            document.querySelectorAll('.card-menu').forEach(menu => menu.classList.remove('show'));
        });

        function unenrollClass(classId) {
            if(confirm("Unenroll from this class?")) {
                localStorage.setItem(classId, 'unenrolled');
                if (currentPage === 'home') navigate('home');
                renderSidebar();
            }
        }

        function restoreClass(classId) {
            localStorage.removeItem(classId);
            if (currentPage === 'unenrolled') navigate('unenrolled');
            renderSidebar();
        }
    </script>
</body>
</html>