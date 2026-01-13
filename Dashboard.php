<?php
session_start();
// Security Check
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
//     header("Location: index.php");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    
    <style>
        /* --- General Reset --- */
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f5f7fa; color: #333; }
        * { box-sizing: border-box; }

        /* --- HEADER --- */
        header {
            background-color: white; padding: 0 40px; height: 70px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid #e0e0e0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            position: sticky; top: 0; z-index: 100;
        }
        .logo-section { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .logo-icon { font-size: 32px; color: #1a73e8; }
        .logo-text { font-size: 24px; font-weight: 700; color: #000; }
        .nav-links { display: flex; gap: 30px; height: 100%; }
        .nav-item {
            display: flex; align-items: center; gap: 8px; text-decoration: none; color: #666;
            font-weight: 500; font-size: 16px; padding: 0 5px; position: relative; height: 100%;
            cursor: pointer;
        }
        .nav-item:hover, .nav-item.active { color: #1a73e8; }
        .nav-item.active::after {
            content: ''; position: absolute; bottom: 0; left: 0;
            width: 100%; height: 3px; background-color: #1a73e8;
        }
        .profile-section { display: flex; align-items: center; gap: 12px; }
        .profile-info { text-align: right; }
        .profile-info h4 { margin: 0; font-size: 14px; color: #333; }
        .profile-info span { font-size: 12px; color: #777; }
        .avatar { width: 40px; height: 40px; border-radius: 50%; background: #ddd url('https://ui-avatars.com/api/?name=Jhomari+Gandionco&background=0D8ABC&color=fff'); background-size: cover; }

        /* Logout Button */
        .logout-btn { margin-left: 15px; background: none; border: none; color: #666; font-size: 20px; cursor: pointer; transition: color 0.2s ease, transform 0.2s ease; display: flex; align-items: center; justify-content: center; padding: 5px; }
        .logout-btn:hover { color: #e74c3c; transform: scale(1.1); }

        /* --- MAIN LAYOUT --- */
        main { max-width: 1200px; margin: 30px auto; padding: 0 20px; }

        /* WIDGETS SECTION */
        .widgets-container { display: grid; grid-template-columns: 1.5fr 0.8fr 1fr; gap: 20px; align-items: start; margin-top: 20px; }
        
        .widget-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e0e0e0; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .widget-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .widget-header h3 { margin: 0; font-size: 18px; font-weight: 500; }
        
        /* View All Link Style */
        .view-all-link { font-size: 13px; color: #1a73e8; text-decoration: none; cursor: pointer; font-weight: 500; }
        .view-all-link:hover { text-decoration: underline; }

        /* Activity List */
        .activity-list { list-style: none; padding: 0; margin: 0; min-height: 100px; }
        .activity-item { display: flex; gap: 15px; margin-bottom: 20px; align-items: center; }
        .activity-avatar { width: 35px; height: 35px; border-radius: 50%; background-color: #eee; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; font-size: 14px; }
        .activity-details p { margin: 0 0 5px 0; font-size: 14px; color: #333; }
        .activity-details span { color: #27ae60; font-weight: 600; }
        .activity-meta { font-size: 12px; color: #999; }

        /* Stats */
        .stats-column { display: flex; flex-direction: column; gap: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid #e0e0e0; display: flex; flex-direction: column; justify-content: center; }
        .stat-icon { font-size: 24px; color: #555; margin-bottom: 10px; }
        .stat-label { font-size: 12px; color: #666; margin-bottom: 5px; }
        .stat-number { font-size: 32px; font-weight: 700; color: #333; }

        /* Calendar */
        .calendar-card { background: white; border-radius: 1px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .calendar-header { background-color: #000; color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
        .calendar-header h3 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; padding: 15px; background: white; }
        .day-name { font-size: 10px; color: #888; margin-bottom: 10px; font-weight: 700; }
        .date { 
            font-size: 14px; padding: 8px; color: #333; border-radius: 50%; width: 35px; height: 35px; 
            display: flex; align-items: center; justify-content: center; margin: 2px auto; position: relative;
        }
        .date.today { background-color: #1a73e8; color: white; font-weight: bold; }
        .date.empty { pointer-events: none; }
        .has-event::after {
            content: ''; position: absolute; bottom: 3px; left: 50%; transform: translateX(-50%);
            width: 4px; height: 4px; background-color: #e37400; border-radius: 50%;
        }

        /* MODAL STYLES (Added for View All) */
        .modal-overlay { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background: white; border-radius: 12px; width: 500px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); max-height: 80vh; overflow-y: auto; position: relative; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .btn-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #666; }
    </style>
</head>
<body>

    <header>
        <div class="logo-section" onclick="window.location.href='Dashboard.php'">
            <i class="fa-solid fa-book-open logo-icon"></i>
            <span class="logo-text">TechHub</span>
        </div>
        <nav class="nav-links">
            <a href="Dashboard.php" class="nav-item active"><i class="fa-solid fa-border-all"></i> Dashboard</a>
            <a href="Classwork.php" class="nav-item"><i class="fa-solid fa-book"></i> Classes</a>
            <a href="gradebook.php" class="nav-item"><i class="fa-solid fa-graduation-cap"></i> Gradebook</a>
        </nav>
        <div class="profile-section">
            <div style="text-align:right;">
                <h4 style="margin:0; font-size:14px;">Prof. Jhomari</h4>
                <span style="font-size:12px; color:#777;">Teacher</span>
            </div>
            <div class="avatar"></div>
            <button class="logout-btn" onclick="handleLogout()" title="Logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </div>
    </header>

    <main>
        <div class="widgets-container">
            
            <div class="widget-card">
                <div class="widget-header">
                    <h3>Recent Student Activity</h3>
                    <span class="view-all-link" onclick="openActivityModal()">View All</span>
                </div>
                <ul class="activity-list" id="activityList">
                    <li style="color:#999; text-align:center; padding:20px;">Loading activity...</li>
                </ul>
            </div>

            <div class="stats-column">
                <div class="stat-card">
                    <i class="fa-regular fa-clock stat-icon"></i>
                    <div class="stat-label">Upcoming Deadlines</div>
                    <div class="stat-number" id="deadlineCount">0</div>
                </div>
                <div class="stat-card">
                    <i class="fa-solid fa-user-graduate stat-icon"></i>
                    <div class="stat-label">Total Students</div>
                    <div class="stat-number" id="studentCount">0</div>
                </div>
            </div>

            <div class="calendar-card">
                <div class="calendar-header">
                    <h3 id="calMonth">MONTH</h3>
                    <span id="calYear">YEAR</span>
                </div>
                <div class="calendar-grid" id="calGrid">
                    <div class="day-name">S</div><div class="day-name">M</div><div class="day-name">T</div><div class="day-name">W</div><div class="day-name">T</div><div class="day-name">F</div><div class="day-name">S</div>
                </div>
            </div>
        </div>
    </main>

    <div id="activityModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0;">All Activity History</h3>
                <button class="btn-close" onclick="closeAllModals()">&times;</button>
            </div>
            <ul class="activity-list" id="fullActivityList">
                <li style="text-align:center; padding:20px; color:#777;">Loading full history...</li>
            </ul>
        </div>
    </div>

<script>
    // --- 1. SUPABASE CONFIG ---
    const supabaseUrl = 'https://nhrcwihvlrybpophbhuq.supabase.co';
    const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5ocmN3aWh2bHJ5YnBvcGhiaHVxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgxOTU1NzgsImV4cCI6MjA4Mzc3MTU3OH0.ByGK-n-gN0APAruRw6c3og5wHCO1zuE7EVSvlT-F6_0';
    let supabaseClient;
    const currentUser = { id: "a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11" };

    try {
        supabaseClient = window.supabase.createClient(supabaseUrl, supabaseKey);
    } catch (e) { console.error("Supabase Init Error:", e); }

    // --- 2. INIT ---
    document.addEventListener('DOMContentLoaded', () => {
        initCalendar();
        if(supabaseClient) {
            fetchRecentActivity();      // Fetches top 5 for widget
            fetchTotalStudentCount();   // Fetches accurate total count
            fetchStatsAndCalendar();
        }
    });

    // --- 3. FETCH RECENT ACTIVITY (Widget Limit 5) ---
    async function fetchRecentActivity() {
        const list = document.getElementById('activityList');
        try {
            const { data: myClasses } = await supabaseClient.from('classes').select('id').eq('teacher_id', currentUser.id);
            if(!myClasses || myClasses.length === 0) {
                list.innerHTML = '<li style="color:#999; text-align:center; padding:15px;">No activity yet.</li>';
                return;
            }
            const classIds = myClasses.map(c => c.id);

            const { data: activity, error } = await supabaseClient
                .from('enrollments')
                .select(`joined_at, student:profiles(full_name), class:classes(title)`)
                .in('class_id', classIds)
                .order('joined_at', { ascending: false })
                .limit(5);

            if (error) throw error;
            renderActivityList(activity, list);

        } catch (err) { console.error("Activity Error:", err); }
    }

    // --- 4. FETCH TOTAL STUDENT COUNT (Syncs unique students) ---
    async function fetchTotalStudentCount() {
        try {
            // 1. Get Teacher's Class IDs
            const { data: classes } = await supabaseClient.from('classes').select('id').eq('teacher_id', currentUser.id);
            if (!classes || classes.length === 0) {
                document.getElementById('studentCount').innerText = 0;
                return;
            }
            const classIds = classes.map(c => c.id);

            // 2. Get All Enrollments
            const { data: enrollments, error } = await supabaseClient
                .from('enrollments')
                .select('student_id')
                .in('class_id', classIds);

            if (error) throw error;

            // 3. Count Unique Students using Set
            const uniqueStudents = new Set(enrollments.map(e => e.student_id));
            document.getElementById('studentCount').innerText = uniqueStudents.size;

        } catch (e) {
            console.error("Count Error:", e);
        }
    }

    // --- 5. OPEN VIEW ALL MODAL ---
    window.openActivityModal = async function() {
        document.getElementById('activityModal').style.display = 'flex';
        const list = document.getElementById('fullActivityList');
        list.innerHTML = '<li style="text-align:center; padding:20px; color:#777;">Loading...</li>';

        try {
            const { data: myClasses } = await supabaseClient.from('classes').select('id').eq('teacher_id', currentUser.id);
            const classIds = myClasses.map(c => c.id);

            // Fetch larger limit (50 items)
            const { data: activity } = await supabaseClient
                .from('enrollments')
                .select(`joined_at, student:profiles(full_name), class:classes(title)`)
                .in('class_id', classIds)
                .order('joined_at', { ascending: false })
                .limit(50);

            renderActivityList(activity, list);

        } catch (err) { 
            list.innerHTML = '<li style="color:red; text-align:center;">Error loading details.</li>';
        }
    }

    // --- HELPER: Render List Items ---
    function renderActivityList(data, container) {
        container.innerHTML = '';
        if (!data || data.length === 0) {
            container.innerHTML = '<li style="color:#999; text-align:center; padding:15px;">No students found.</li>';
            return;
        }

        data.forEach(act => {
            const li = document.createElement('li');
            li.className = 'activity-item';
            
            const date = new Date(act.joined_at);
            const now = new Date();
            const diffMins = Math.floor((now - date) / 60000); 
            let timeStr = date.toLocaleDateString();
            if(diffMins < 1) timeStr = "Just now";
            else if(diffMins < 60) timeStr = `${diffMins} mins ago`;
            else if(diffMins < 1440) timeStr = `${Math.floor(diffMins/60)} hours ago`;

            const name = act.student ? act.student.full_name : "Unknown";
            const className = act.class ? act.class.title : "Class";
            const colors = ['#e74c3c', '#3498db', '#f1c40f', '#9b59b6', '#2ecc71'];
            const randColor = colors[Math.floor(Math.random() * colors.length)];
            const initial = name.charAt(0).toUpperCase();

            li.innerHTML = `
                <div class="activity-avatar" style="background-color:${randColor}; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:14px; border-radius:50%; width:35px; height:35px;">${initial}</div>
                <div class="activity-details">
                    <p style="margin:0 0 5px 0; font-size:14px; color:#333;"><strong>${name}</strong> joined <span style="color:#27ae60; font-weight:600;">${className}</span></p>
                    <div class="activity-meta" style="font-size:12px; color:#999;">${timeStr}</div>
                </div>
            `;
            container.appendChild(li);
        });
    }

    // --- 6. OTHER FUNCTIONS ---
    async function fetchStatsAndCalendar() {
        try {
            const { data: deadlines, error } = await supabaseClient
                .from('classwork')
                .select('due_date')
                .eq('teacher_id', currentUser.id)
                .not('due_date', 'is', null)
                .gte('due_date', new Date().toISOString());

            if (error) throw error;
            document.getElementById('deadlineCount').innerText = deadlines ? deadlines.length : 0;
            const eventDates = deadlines.map(d => new Date(d.due_date).getDate());
            renderCalendar(eventDates);
        } catch (err) { console.error("Stats Error:", err); }
    }

    function initCalendar() { renderCalendar([]); }

    function renderCalendar(activeDays) {
        const date = new Date();
        const months = ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"];
        document.getElementById('calMonth').innerText = months[date.getMonth()];
        document.getElementById('calYear').innerText = date.getFullYear();

        const grid = document.getElementById('calGrid');
        grid.innerHTML = '<div class="day-name">S</div><div class="day-name">M</div><div class="day-name">T</div><div class="day-name">W</div><div class="day-name">T</div><div class="day-name">F</div><div class="day-name">S</div>';
        
        const firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getDay();
        const lastDate = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
        const today = date.getDate();

        for(let i = 0; i < firstDay; i++) {
            const div = document.createElement('div');
            div.className = 'date empty';
            grid.appendChild(div);
        }
        for(let i = 1; i <= lastDate; i++) {
            const div = document.createElement('div');
            div.className = 'date';
            div.innerText = i;
            if(i === today) div.classList.add('today');
            if (activeDays.includes(i)) div.classList.add('has-event');
            grid.appendChild(div);
        }
    }

    function closeAllModals() { document.querySelectorAll('.modal-overlay').forEach(e => e.style.display = 'none'); }
    window.onclick = function(e) { if(e.target.classList.contains('modal-overlay')) closeAllModals(); }

    async function handleLogout() {
        if (confirm("Are you sure you want to log out?")) {
            try {
                if (supabaseClient.auth) await supabaseClient.auth.signOut();
                window.location.href = 'index.php';
            } catch (err) {
                console.error("Logout Error:", err);
                window.location.href = 'index.php';
            }
        }
    }
</script>
</body>
</html>