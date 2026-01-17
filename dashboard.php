<?php
session_start();

// Proteksi Halaman
if (!isset($_SESSION['status_login'])) {
    header("Location: index.php");
    exit();
}

// 1. Inisialisasi Data Mata Kuliah
if (!isset($_SESSION['data_mk'])) {
    $_SESSION['data_mk'] = [
        "Rekayasa Perangkat Lunak" => ["sks" => 2, "materi" => ""],
        "Praktikum RPL" => ["sks" => 1, "materi" => ""],
        "Organisasi dan Arsitektur Komputer" => ["sks" => 3, "materi" => ""],
        "Sistem Basis Data" => ["sks" => 2, "materi" => ""],
        "Praktikum Basis Data" => ["sks" => 1, "materi" => ""],
        "Sistem Operasi" => ["sks" => 2, "materi" => ""],
        "Algoritma dan Pemrograman" => ["sks" => 3, "materi" => ""],
        "Sistem Tertanam" => ["sks" => 2, "materi" => ""],
        "Matematika Teknik" => ["sks" => 2, "materi" => ""],
        "Praktikum SDL" => ["sks" => 1, "materi" => ""],
        "Transduser dan Sensor" => ["sks" => 2, "materi" => ""]
    ];
}

// 2. Inisialisasi To-Do List
if (!isset($_SESSION['tugas'])) {
    $_SESSION['tugas'] = [];
}

// 3. Logika Simpan Materi
if (isset($_POST['update_materi'])) {
    $_SESSION['data_mk'][$_POST['nama_mk']]['materi'] = htmlspecialchars($_POST['materi_baru']);
    header("Location: view_materi.php");
    exit();
}

// 4. Logika Tambah Tugas
if (isset($_POST['tambah_tugas'])) {
    $_SESSION['tugas'][] = [
        "teks" => htmlspecialchars($_POST['isi_tugas']), 
        "deadline" => $_POST['deadline'],
        "done" => false 
    ];
    usort($_SESSION['tugas'], function($a, $b) { 
        return strtotime($a['deadline']) - strtotime($b['deadline']); 
    });
}

// Fitur Centang (Done)
if (isset($_GET['check'])) {
    $idx = $_GET['check'];
    if (isset($_SESSION['tugas'][$idx])) {
        $_SESSION['tugas'][$idx]['done'] = !$_SESSION['tugas'][$idx]['done'];
    }
    header("Location: dashboard.php");
    exit();
}

// 5. Logika Hapus Tugas
if (isset($_GET['hapus'])) {
    unset($_SESSION['tugas'][$_GET['hapus']]);
    $_SESSION['tugas'] = array_values($_SESSION['tugas']);
    header("Location: dashboard.php");
    exit();
}

$tugas_list = $_SESSION['tugas'];
usort($tugas_list, function($a, $b) {
    return $a['done'] <=> $b['done'];
});
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Akademik</title>
    <style>
        :root { 
            --gradasi: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); 
            --biru-ungu: #6a11cb;
            --biru-indigo: #3f51b5;
        }

        body { 
            font-family: 'Times New Roman', Times, serif; 
            background: #f0f2f5; 
            margin: 0; 
            padding: 30px 20px; 
        }

        body::before {
            content: "";
            background-image: url('images/undip.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            filter: blur(8px); 
            -webkit-filter: blur(8px);
            transform: scale(1.1);
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
        }
        
        .header-card { 
            background: var(--gradasi); 
            color: white; 
            padding: 50px 40px; 
            border-radius: 15px; 
            max-width: 1020px; 
            margin: 0 auto 30px auto; 
            position: relative;
            box-shadow: 0 8px 25px rgba(106, 17, 203, 0.3);
        }

        .top-nav { position: absolute; top: 15px; right: 20px; display: flex; gap: 10px; }
        .nav-btn { color: white; text-decoration: none; border: 1px solid rgba(255,255,255,0.5); padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; background: rgba(255,255,255,0.1); font-weight: bold; transition: 0.3s; }
        .nav-btn:hover { background: white; color: var(--biru-ungu); transform: translateY(-2px); }

        .container { 
            max-width: 1100px; 
            margin: 0 auto; 
            display: grid; 
            grid-template-columns: 1.4fr 1fr; 
            gap: 25px; 
        }
        
        .card {
            background: rgba(255, 255, 255, 0.8) !important; 
            backdrop-filter: blur(12px); 
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .card-title { font-weight: bold; border-bottom: 2px solid rgba(0,0,0,0.05); padding-bottom: 12px; margin-bottom: 20px; font-size: 1.3rem; color: var(--biru-indigo); }

        .todo-input { 
            width: 100%; 
            padding: 12px; 
            margin-bottom: 15px; 
            border: 1px solid rgba(255, 255, 255, 0.5); 
            border-radius: 10px; 
            font-family: inherit; 
            box-sizing: border-box; 
            transition: all 0.4s ease; 
            outline: none;
            background: rgba(255, 255, 255, 0.2) !important; 
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            color: #333 !important;
        }

        .todo-input::placeholder { color: rgba(0, 0, 0, 0.5); }

        .todo-input:focus, .todo-input:hover {
            transform: translateY(-2px);
            border-color: var(--biru-ungu);
            background: rgba(255, 255, 255, 0.4) !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); 
        }

        .btn-add { 
            background: var(--gradasi); color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; 
            box-shadow: 0 4px 15px rgba(106, 17, 203, 0.3);
        }
        .btn-add:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(106, 17, 203, 0.5); }

        .btn-save { 
            background: var(--gradasi); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 10px; transition: 0.3s;
            box-shadow: 0 4px 12px rgba(106, 17, 203, 0.2); 
        }
        .btn-save:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(106, 17, 203, 0.4); }

        .mk-item { border-radius: 12px; margin-bottom: 15px; overflow: hidden; background: white; transition: 0.3s; border: 1px solid #eee; }
        .mk-item:hover, .mk-item.active { transform: translateY(-5px); box-shadow: 0 12px 25px rgba(106, 17, 203, 0.2); }
        .mk-header { padding: 18px; cursor: pointer; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
        .mk-item:hover .mk-header, .mk-item.active .mk-header { background: var(--gradasi); color: white; }
        
        .mk-body { max-height: 0; overflow: hidden; transition: 0.4s ease; background: #fff; }
        .mk-item.active .mk-body { max-height: 500px; padding: 20px; border-top: 1px solid #eee; }

        /* --- UPDATE: TAMPILAN TUGAS --- */
        .task-card { 
            background: rgba(106, 17, 203, 0.15) !important; /* Biru Ungu Transparan */
            border-left: 5px solid var(--biru-ungu); 
            padding: 15px; 
            margin-top: 10px; 
            border-radius: 8px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            backdrop-filter: blur(5px);
            transition: 0.3s ease;
        }

        /* --- UPDATE: EFEK TULISAN DICORET --- */
        .task-card.is-done { 
            background: rgba(0, 0, 0, 0.05) !important; 
            border-left: 5px solid #bbb; 
            opacity: 0.6;
        }
        .task-card.is-done span { 
            text-decoration: line-through; 
            color: #777; 
        }
        
        .editor-toolbar { background: #f8f9fa; border: 1px solid #ddd; border-bottom: none; padding: 5px; border-radius: 10px 10px 0 0; display: flex; gap: 5px; }
        .rich-editor { min-height: 100px; border: 1px solid #ddd; padding: 10px; border-radius: 0 0 10px 10px; outline: none; background: white; }

        @media (max-width: 800px) { .container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <div class="header-card">
        <div class="top-nav">
            <a href="index.php" class="nav-btn">Home</a>
            <a href="logout.php" class="nav-btn">Logout</a>
        </div>
        <h1 style="margin:0; font-size:2.5rem; text-transform:uppercase; letter-spacing:2px;">OKTAVIA DAMAYANTI</h1>
        <p style="margin:10px 0 0 0; opacity:0.9; font-size: 1.1rem;">NIM: 21120124120025 | Teknik Komputer S1 | Semester 4</p>
    </div>

    <div class="container">
        <div class="left-col">
            <div class="card">
                <div class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
                    Mata Kuliah
                    <a href="view_materi.php" style="font-size: 0.8rem; color: var(--biru-indigo); text-decoration: none; padding: 6px 14px; border-radius: 8px; background: #fff; border: 1px solid #ddd; font-weight: bold;">See Notes</a>
                </div>
                <?php foreach ($_SESSION['data_mk'] as $nama => $detail): ?>
                    <div class="mk-item">
                        <div class="mk-header" onclick="this.parentElement.classList.toggle('active')">
                            <div>
                                <span><?php echo $nama; ?></span>
                                <div style="font-size: 0.75rem; opacity: 0.7; font-weight: normal;"><?php echo $detail['sks']; ?> SKS | Semester 4</div>
                            </div>
                            <span>▼</span>
                        </div>
                        <div class="mk-body">
                            <form method="POST">
                                <input type="hidden" name="nama_mk" value="<?php echo $nama; ?>">
                                <textarea name="materi_baru" style="display:none;"><?php echo $detail['materi']; ?></textarea>
                                <div style="text-align: right;">
                                    <button type="submit" name="update_materi" class="btn-save">Save & View Notes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="right-col">
            <div class="card">
                <div class="card-title">To-Do List Tugas</div>
                <form method="POST">
                    <input type="text" name="isi_tugas" class="todo-input" placeholder="Tugas baru..." required>
                    <input type="date" name="deadline" class="todo-input" required>
                    <button type="submit" name="tambah_tugas" class="btn-add">Add to List</button>
                </form>

                <div style="margin-top: 20px;">
                    <?php if (empty($tugas_list)): ?>
                        <p style="text-align: center; color: #999; font-style: italic;">Kosong.</p>
                    <?php else: ?>
                        <?php foreach ($tugas_list as $idx => $t): ?>
                            <?php $original_idx = array_search($t, $_SESSION['tugas']); ?>
                            <div class="task-card <?php echo $t['done'] ? 'is-done' : ''; ?>">
                                <div style="display: flex; align-items: center;">
                                    <a href="?check=<?php echo $original_idx; ?>" style="text-decoration: none; font-size: 1.2rem; margin-right: 10px; color: #4caf50;">
                                        <?php echo $t['done'] ? '✔' : '○'; ?>
                                    </a>
                                    <div>
                                        <small style="color: #d32f2f; font-weight: bold;">⏳ <?php echo date('d M Y', strtotime($t['deadline'])); ?></small><br>
                                        <span><?php echo $t['teks']; ?></span>
                                    </div>
                                </div>
                                <a href="?hapus=<?php echo $original_idx; ?>" style="color: #ff4d4d; text-decoration: none; font-weight: bold; font-size: 1.4rem;" onclick="return confirm('Hapus?')">&times;</a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('textarea[name="materi_baru"]').forEach(area => {
            const toolbar = document.createElement('div');
            toolbar.className = 'editor-toolbar';
            toolbar.innerHTML = `
                <button type="button" onclick="document.execCommand('bold',false,null)">B</button>
                <button type="button" onclick="document.execCommand('italic',false,null)">I</button>
                <button type="button" onclick="document.execCommand('underline',false,null)">U</button>
                <button type="button" onclick="document.execCommand('insertUnorderedList',false,null)">• List</button>
            `;

            const editor = document.createElement('div');
            editor.className = 'rich-editor';
            editor.contentEditable = true;
            editor.innerHTML = area.value;

            area.parentNode.insertBefore(toolbar, area);
            area.parentNode.insertBefore(editor, area);

            editor.addEventListener('input', () => {
                area.value = editor.innerHTML;
            });
        });
    </script>
</body>
</html>