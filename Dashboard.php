<?php
// We don't strictly need PHP session if we use Supabase Auth, 
// but it's good to keep for hybrid apps.
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
        .avatar { width: 40px; height: 40px; border-radius: 50%; background-color: #ddd; background-size: cover; background-position: center; }
        
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

        /* ACTIVITY */
        .activity-card { background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 25px; height: auto; min-height: 300px; }
        .activity-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px; }
        .activity-header h3 { margin: 0; font-weight: 600; font-size: 18px; }
        
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

        /* LOADING OVERLAY */
        #loading-screen { position: fixed; inset: 0; background: white; z-index: 999; display: flex; justify-content: center; align-items: center; font-size: 18px; color: #666; }
    </style>
</head>

<body>
    <div id="loading-screen">Loading your Dashboard...</div>

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
                <h4 id="profile-name" style="margin:0; font-size:14px;">Loading...</h4>
                <span style="font-size:12px; color:#777;">Teacher</span>
            </div>
            <div class="avatar" id="profile-avatar"></div>
            <button class="logout-btn" onclick="handleLogout()" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></button>
        </div>
    </header>

    <main>
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-regular fa-clock"></i></div>
                <div class="stat-label">Upcoming Deadlines</div>
                <div class="stat-number" id="deadlineCount">0</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div>
                <div class="stat-label">Total Students</div>
                <div class="stat-number" id="studentCount">0</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                <div class="stat-label">My Classes</div>
                <div class="stat-number" id="classCount">0</div>
            </div>
        </div>

        <div class="bottom-grid">
            <div class="activity-card">
                <div class="activity-header">
                    <h3>Recent Student Activity</h3>
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

    <script>
        // 1. Initialize Supabase
        const supabaseUrl = 'https://nhrcwihvlrybpophbhuq.supabase.co';
        const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5ocmN3aWh2bHJ5YnBvcGhiaHVxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgxOTU1NzgsImV4cCI6MjA4Mzc3MTU3OH0.ByGK-n-gN0APAruRw6c3og5wHCO1zuE7EVSvlT-F6_0';
        const _supabase = supabase.createClient(supabaseUrl, supabaseKey);

        let currentUser = null;

        // 2. Main Loader
        document.addEventListener('DOMContentLoaded', async () => {
            // Check Session
            const { data: { session } } = await _supabase.auth.getSession();

            if (!session) {
                // Not logged in? Go to login
                window.location.href = 'teacher_login.php';
                return;
            }

            currentUser = session.user;

            // Load all data in parallel
            await Promise.all([
                fetchUserProfile(),
                fetchClassCount(),
                fetchTotalStudentCount(),
                fetchStatsAndCalendar(),
                fetchRecentActivity()
            ]);
            
            initCalendar();

            // Hide loading screen
            document.getElementById('loading-screen').style.display = 'none';
        });

        // 3. Fetch User Profile (Name & Avatar)
        async function fetchUserProfile() {
            try {
                const { data: profile, error } = await _supabase
                    .from('profiles')
                    .select('full_name, role')
                    .eq('id', currentUser.id)
                    .single();
                
                if (profile) {
                    // Update Name
                    document.getElementById('profile-name').innerText = profile.full_name;
                    
                    // Update Avatar (Generates a colorful image with their initials)
                    const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(profile.full_name)}&background=0D8ABC&color=fff`;
                    document.getElementById('profile-avatar').style.backgroundImage = `url('${avatarUrl}')`;
                }
            } catch (err) {
                console.error("Profile Error:", err);
            }
        }

        // 4. Fetch Count of Classes for THIS teacher
        async function fetchClassCount() {
            const { count } = await _supabase
                .from('classes')
                .select('*', { count: 'exact', head: true })
                .eq('teacher_id', currentUser.id); // <--- Uses dynamic ID
            
            document.getElementById('classCount').innerText = count || 0;
        }

        // 5. Fetch Count of Unique Students
        async function fetchTotalStudentCount() {
            try {
                // Get my classes first
                const { data: classes } = await _supabase.from('classes').select('id').eq('teacher_id', currentUser.id);
                if (!classes || classes.length === 0) return;

                const classIds = classes.map(c => c.id);

                // Get enrollments for those classes
                const { data: enrollments } = await _supabase.from('enrollments').select('student_id').in('class_id', classIds);
                
                // Count unique students
                const uniqueStudents = new Set(enrollments.map(e => e.student_id));
                document.getElementById('studentCount').innerText = uniqueStudents.size;
            } catch (e) { console.error(e); }
        }

        // 6. Fetch Recent Activity
        async function fetchRecentActivity() {
            const list = document.getElementById('activityList');
            try {
                const { data: myClasses } = await _supabase.from('classes').select('id').eq('teacher_id', currentUser.id);
                
                if (!myClasses || myClasses.length === 0) {
                    list.innerHTML = '<p style="color:#999; text-align:center; padding:15px;">No activity yet.</p>';
                    return;
                }

                const classIds = myClasses.map(c => c.id);

                // Fetch enrollments (or you could fetch 'submissions' if you have that table)
                const { data: activity, error } = await _supabase
                    .from('enrollments')
                    .select(`joined_at, student:profiles(full_name), class:classes(title)`) // Ensure these relationships exist in Supabase!
                    .in('class_id', classIds)
                    .order('joined_at', { ascending: false })
                    .limit(5);

                if (error) throw error;
                renderActivityList(activity, list);

            } catch (err) { console.error("Activity Error:", err); }
        }

        function renderActivityList(data, container) {
            container.innerHTML = '';
            if (!data || data.length === 0) {
                container.innerHTML = '<p style="color:#999; text-align:center; padding:15px;">No recent activity.</p>';
                return;
            }
            data.forEach(act => {
                const div = document.createElement('div');
                div.className = 'activity-item';
                const date = new Date(act.joined_at);
                const name = act.student ? act.student.full_name : "Unknown Student";
                const className = act.class ? act.class.title : "Class";
                const initial = name.charAt(0).toUpperCase();

                div.innerHTML = `
                    <div style="background:#1a73e8; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:14px; border-radius:50%; width:35px; height:35px; flex-shrink:0;">${initial}</div>
                    <div class="activity-details">
                        <p><strong>${name}</strong> joined <span style="color:#1a73e8; font-weight:600;">${className}</span></p>
                        <div class="activity-meta">${date.toLocaleDateString()}</div>
                    </div>`;
                container.appendChild(div);
            });
        }

        // 7. Stats & Calendar
        async function fetchStatsAndCalendar() {
            try {
                // Assuming you have a 'classwork' table
                const { data: deadlines } = await _supabase
                    .from('classwork')
                    .select('due_date')
                    .eq('teacher_id', currentUser.id)
                    .not('due_date', 'is', null)
                    .gte('due_date', new Date().toISOString());

                document.getElementById('deadlineCount').innerText = deadlines ? deadlines.length : 0;
                
                const eventDates = deadlines ? deadlines.map(d => new Date(d.due_date).getDate()) : [];
                renderCalendar(eventDates);
            } catch (err) { 
                // If table doesn't exist yet, ignore error
                console.log("Classwork table might not exist yet."); 
            }
        }

        // 8. Calendar UI Logic
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

        // 9. Logout
        async function handleLogout() {
            if (confirm("Are you sure you want to log out?")) {
                await _supabase.auth.signOut();
                window.location.href = 'teacher_login.php';
            }
        }
    </script>
</body>
</html>