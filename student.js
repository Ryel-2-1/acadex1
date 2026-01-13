// --- 1. DATA ---
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
let currentTab = 'stream'; 

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

    contentDiv.innerHTML = `
        <nav class="class-nav-tabs">
            <div class="nav-tab ${currentTab === 'stream' ? 'active-tab' : ''}" onclick="switchClassTab('stream')">Stream</div>
            <div class="nav-tab ${currentTab === 'classwork' ? 'active-tab' : ''}" onclick="switchClassTab('classwork')">Classwork</div>
            <div class="nav-tab ${currentTab === 'people' ? 'active-tab' : ''}" onclick="switchClassTab('people')">People</div>
        </nav>
    `;

    if (currentTab === 'stream') {
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