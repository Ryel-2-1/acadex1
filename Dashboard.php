<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TechHub - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

    <style>
        /* --- GENERAL RESET --- */
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f5f7fa; color: #333; min-height: 100vh; display: flex; flex-direction: column; }
        * { box-sizing: border-box; }

        /* --- HEADER --- */
        header { background: white; padding: 0 40px; height: 70px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0e0e0; flex-shrink: 0; z-index: 100; position: sticky; top: 0; }
        .logo-section { display: flex; align-items: center; gap: 10px; width: 250px; cursor: pointer; }
        .logo-icon { font-size: 32px; color: #1a73e8; }
        .logo-text { font-size: 24px; font-weight: 700; color: #000; }
        
        .nav-links { display: flex; gap: 30px; height: 100%; }
        .nav-item { display: flex; align-items: center; gap: 8px; text-decoration: none; color: #666; font-weight: 500; height: 100%; cursor: pointer; border-bottom: 3px solid transparent; transition: 0.2s; }
        .nav-item.active { color: #1a73e8; border-bottom: 3px solid #1a73e8; }
        .nav-item:hover { color: #1a73e8; }

        .profile-section { display: flex; align-items: center; gap: 12px; width: 250px; justify-content: flex-end; }
        .avatar { width: 40px; height: 40px; border-radius: 50%; background: #ddd url('https://ui-avatars.com/api/?name=Jhomari+Gandionco&background=0D8ABC&color=fff'); background-size: cover; }
        
        .logout-btn { margin-left: 15px; background: none; border: none; color: #666; font-size: 20px; cursor: pointer; transition: 0.2s; padding: 5px; }
        .logout-btn:hover { color: #e74c3c; transform: scale(1.1); }

        /* --- MAIN CONTENT --- */
        main { flex: 1; max-width: 1100px; margin: 40px auto; padding: 0 20px; width: 100%; }

        /* STATS */
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 40px; }
        .stat-card { background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .stat-icon { width: 50px; height: 50px; border-radius: 50%; background: #f8f9fa; border: 1px solid #eee; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; color: #1a73e8; font-size: 20px; }
        .stat-label { font-size: 14px; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-number { font-size: 48px; font-weight: 700; color: #1a73e8; }

        /* BOTTOM GRID */
        .bottom-grid { display: grid; grid-template-columns: 1.2fr .8fr; gap: 30px; align-items: start; }

        /* ACTIVITY (No scrollbar) */
        .activity-card { background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 25px; height: auto; overflow: visible; }
        .activity-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px; }
        .activity-header h3 { margin: 0; font-weight: 600; font-size: 18px; }
        
        #activityList { height: auto; overflow: visible; }
        .activity-item { display: flex; gap: 15px; margin-bottom: 20px; align-items: flex-start; }
        .activity-details p { margin: 0; font-size: 14px; line-height: 1.4; }
        .activity-meta { font-size: 12px; color: #999; margin-top: 4px; }

        /* CALENDAR */
        .calendar-card { background: white; border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden; height: auto; }
        .calendar-header { background: #1a73e8; color: #fff; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .calendar-header h3 { margin: 0; font-size: 16px; letter-spacing: 1px; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); padding: 15px; text-align: center; }
        .day-name { font-size: 11px; font-weight: 700; color: #bbb; padding-bottom: 10px; }
        .date { font-size: 13px; font-weight: 500; padding: 8px; border-radius: 50%; }
        .date.today { background: #e8f0fe; color: #1a73e8; font-weight: 700; }
        .has-event { position: relative; color: #1a73e8; font-weight: 700; }
        .has-event::after { content: ''; width: 4px; height: 4px; background: #e37400; border-radius: 50%; position: absolute; bottom: 4px; left: 50%; transform: translateX(-50%); }

        /* MODALS */
        .modal-overlay { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; }
        .modal-content { background: white; border-radius: 8px; width: 500px; max-height: 85vh; display: flex; flex-direction: column; overflow: hidden; }
        .modal-header { padding: 15px 24px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 24px; overflow-y: auto; }
        
    </style>
</head>

<body>

    <header>
        <div class="logo-section" onclick="window.location.href='Dashboard.php'">
            <i class="fa-solid fa-book-open logo-icon"></i><span class="logo-text">TechHub</span>
        </div>
        <nav class="nav-links">
            <a href="Dashboard.php" class="nav-item active">Dashboard</a>
            <a href="Classwork.php" class="nav-item">Classes</a>
            <a href="gradebook.php" class="nav-item">Gradebook</a>
        </nav>
        <div class="profile-section">
            <div style="text-align:right;">
                <h4 style="margin:0; font-size:14px;">Prof. Jhomari</h4>
                <span style="font-size:12px; color:#777;">Teacher</span>
            </div>
            <div class="avatar"></div>
            <button class="logout-btn" onclick="handleLogout()" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></button>
        </div>
    </header>

    <main>
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-regular fa-clock"></i></div>
                <div class="stat-label">Upcoming</div>
                <div class="stat-number" id="deadlineCount">0</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div>
                <div class="stat-label">Total Students</div>
                <div class="stat-number" id="studentCount">0</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                <div class="stat-label">Total Classes</div>
                <div class="stat-number" id="classCount">0</div>
            </div>
        </div>

        <div class="bottom-grid">
            <div class="activity-card">
                <div class="activity-header">
                    <h3>Recent Student Activity</h3>
                    <button onclick="openActivityModal()" style="background:none; border:none; color:#1a73e8; font-weight:600; cursor:pointer; font-size:14px;">
                        View All
                    </button>
                </div>
                <div id="activityList">
                    <p style="color:#999">Loading activity...</p>
                </div>
            </div>

            <div class="calendar-card">
                <div class="calendar-header">
                    <h3 id="calMonth">MONTH</h3>
                    <span id="calYear">2026</span>
                </div>
                <div class="calendar-grid" id="calGrid"></div>
            </div>
        </div>
    </main>

    <div id="activityModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="margin:0; font-size:20px;">All Student Activity</h2>
                <span style="cursor:pointer; font-size:24px;" onclick="closeAllModals()">&times;</span>
            </div>
            <div class="modal-body">
                <div id="fullActivityList"></div>
            </div>
        </div>
    </div>

    <script>
        const supabaseUrl = 'https://nhrcwihvlrybpophbhuq.supabase.co';
        const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5ocmN3aWh2bHJ5YnBvcGhiaHVxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgxOTU1NzgsImV4cCI6MjA4Mzc3MTU3OH0.ByGK-n-gN0APAruRw6c3og5wHCO1zuE7EVSvlT-F6_0';
        let supabaseClient;
        const currentUser = { id: "a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11" };

        try {
            supabaseClient = window.supabase.createClient(supabaseUrl, supabaseKey);
        } catch (e) { console.error("Supabase Init Error:", e); }

        document.addEventListener('DOMContentLoaded', () => {
            initCalendar();
            if (supabaseClient) {
                fetchRecentActivity();
                fetchTotalStudentCount();
                fetchStatsAndCalendar();
                fetchClassCount();
            }
        });

        async function fetchClassCount() {
            const { count } = await supabaseClient.from('classes').select('*', { count: 'exact', head: true }).eq('teacher_id', currentUser.id);
            document.getElementById('classCount').innerText = count || 0;
        }

        async function fetchRecentActivity() {
            const list = document.getElementById('activityList');
            try {
                const { data: myClasses } = await supabaseClient.from('classes').select('id').eq('teacher_id', currentUser.id);
                if (!myClasses || myClasses.length === 0) {
                    list.innerHTML = '<p style="color:#999; text-align:center; padding:15px;">No activity yet.</p>';
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

        async function fetchTotalStudentCount() {
            try {
                const { data: classes } = await supabaseClient.from('classes').select('id').eq('teacher_id', currentUser.id);
                if (!classes || classes.length === 0) return;
                const classIds = classes.map(c => c.id);
                const { data: enrollments } = await supabaseClient.from('enrollments').select('student_id').in('class_id', classIds);
                const uniqueStudents = new Set(enrollments.map(e => e.student_id));
                document.getElementById('studentCount').innerText = uniqueStudents.size;
            } catch (e) { console.error(e); }
        }

        window.openActivityModal = async function () {
            document.getElementById('activityModal').style.display = 'flex';
            const list = document.getElementById('fullActivityList');
            list.innerHTML = '<p style="text-align:center; padding:20px; color:#777;">Loading...</p>';
            try {
                const { data: myClasses } = await supabaseClient.from('classes').select('id').eq('teacher_id', currentUser.id);
                const classIds = myClasses.map(c => c.id);
                const { data: activity } = await supabaseClient
                    .from('enrollments')
                    .select(`joined_at, student:profiles(full_name), class:classes(title)`)
                    .in('class_id', classIds)
                    .order('joined_at', { ascending: false })
                    .limit(50);
                renderActivityList(activity, list);
            } catch (err) { list.innerHTML = '<p style="color:red; text-align:center;">Error loading details.</p>'; }
        }

        function renderActivityList(data, container) {
            container.innerHTML = '';
            if (!data || data.length === 0) {
                container.innerHTML = '<p style="color:#999; text-align:center; padding:15px;">No students found.</p>';
                return;
            }
            data.forEach(act => {
                const div = document.createElement('div');
                div.className = 'activity-item';
                const date = new Date(act.joined_at);
                const name = act.student ? act.student.full_name : "Unknown";
                const className = act.class ? act.class.title : "Class";
                const initial = name.charAt(0).toUpperCase();

                div.innerHTML = `
                    <div style="background:#1a73e8; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:14px; border-radius:50%; width:35px; height:35px; flex-shrink:0;">${initial}</div>
                    <div class="activity-details">
                        <p><strong>${name}</strong> joined <span style="color:#1a73e8; font-weight:600;">${className}</span></p>
                        <div class="activity-meta">${date.toLocaleDateString()} at ${date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                    </div>`;
                container.appendChild(div);
            });
        }

        async function fetchStatsAndCalendar() {
            try {
                const { data: deadlines } = await supabaseClient
                    .from('classwork')
                    .select('due_date')
                    .eq('teacher_id', currentUser.id)
                    .not('due_date', 'is', null)
                    .gte('due_date', new Date().toISOString());

                document.getElementById('deadlineCount').innerText = deadlines ? deadlines.length : 0;
                const eventDates = deadlines ? deadlines.map(d => new Date(d.due_date).getDate()) : [];
                renderCalendar(eventDates);
            } catch (err) { console.error(err); }
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

            for (let i = 0; i < firstDay; i++) { grid.appendChild(document.createElement('div')); }
            for (let i = 1; i <= lastDate; i++) {
                const div = document.createElement('div');
                div.className = 'date';
                div.innerText = i;
                if (i === today) div.classList.add('today');
                if (activeDays.includes(i)) div.classList.add('has-event');
                grid.appendChild(div);
            }
        }

        function closeAllModals() { document.querySelectorAll('.modal-overlay').forEach(e => e.style.display = 'none'); }
        window.onclick = function (e) { if (e.target.classList.contains('modal-overlay')) closeAllModals(); }

        async function handleLogout() {
            if (confirm("Are you sure you want to log out?")) {
                try {
                    if (supabaseClient.auth) await supabaseClient.auth.signOut();
                    window.location.href = 'index.php';
                } catch (err) { window.location.href = 'index.php'; }
            }
        }
    </script>
</body>
</html>