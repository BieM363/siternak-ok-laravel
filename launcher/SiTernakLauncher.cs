using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.Net;
using System.Net.NetworkInformation;
using System.Net.Sockets;
using System.Text;
using System.Threading;
using System.Windows.Forms;

namespace SiTernakLauncher
{
    public class MainForm : Form
    {
        private string projectRoot;
        private string phpPath;
        private int serverPort = 8000;
        private string lanIp = "127.0.0.1";

        private Process webServerProcess;
        private Process schedulerProcess;
        private System.Windows.Forms.Timer cronWatchTimer;
        private bool isShuttingDown = false;
        private bool browserOpenedOnce = false;

        // UI Controls
        private Label lblWebStatus;
        private Label lblCronStatus;
        private Label lblLocalUrl;
        private Label lblLanUrl;
        private TextBox txtLog;
        private Button btnOpenWeb;
        private Button btnOpenLogin;
        private Button btnTriggerNotif;
        private Button btnCopyLan;
        private Button btnRestart;
        private Button btnStop;
        private NotifyIcon trayIcon;

        public MainForm(bool autoOpenBrowser)
        {
            this.projectRoot = DetectProjectRoot();
            this.phpPath = DetectPhpExecutable();
            this.lanIp = DetectLocalLanIp();

            InitializeUi();
            InitializeTrayIcon();

            this.Shown += (s, e) =>
            {
                ThreadPool.QueueUserWorkItem(_ => StartAllServices(autoOpenBrowser));
            };

            this.FormClosing += (s, e) =>
            {
                isShuttingDown = true;
                StopAllServices();
                if (trayIcon != null)
                {
                    trayIcon.Visible = false;
                    trayIcon.Dispose();
                }
            };
        }

        private void InitializeUi()
        {
            this.Text = "SiTernak-OK — Peluncur Web & Server Notifikasi (Dinas Peternakan Gorontalo)";
            this.Size = new Size(780, 560);
            this.MinimumSize = new Size(740, 520);
            this.StartPosition = FormStartPosition.CenterScreen;
            this.BackColor = Color.FromArgb(241, 245, 249); // Slate-100
            this.Font = new Font("Segoe UI", 9.5F, FontStyle.Regular);
            this.Icon = SystemIcons.Shield;

            // Header Banner Panel
            Panel headerPanel = new Panel
            {
                Dock = DockStyle.Top,
                Height = 76,
                BackColor = Color.FromArgb(15, 23, 42), // Slate-900
                Padding = new Padding(20, 14, 20, 14)
            };

            Label lblTitle = new Label
            {
                Text = "SiTernak-OK — Server Web & Cronjob Notifikasi 8 Jam",
                ForeColor = Color.White,
                Font = new Font("Segoe UI Semibold", 13.5F, FontStyle.Bold),
                AutoSize = true,
                Location = new Point(20, 14)
            };

            Label lblSubtitle = new Label
            {
                Text = "Dinas Peternakan dan Perkebunan Provinsi Gorontalo • Sistem Pemantauan IB & Kelahiran Sapi",
                ForeColor = Color.FromArgb(16, 185, 129), // Emerald-500
                Font = new Font("Segoe UI", 9.0F, FontStyle.Regular),
                AutoSize = true,
                Location = new Point(20, 44)
            };

            headerPanel.Controls.Add(lblTitle);
            headerPanel.Controls.Add(lblSubtitle);

            // Status Card Panel
            Panel statusCard = new Panel
            {
                Location = new Point(18, 90),
                Size = new Size(728, 122),
                Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right,
                BackColor = Color.White,
                BorderStyle = BorderStyle.FixedSingle
            };

            lblWebStatus = new Label
            {
                Text = "Status Web Server: Menyiapkan layanan...",
                Font = new Font("Segoe UI Semibold", 10.0F, FontStyle.Bold),
                ForeColor = Color.FromArgb(217, 119, 6),
                AutoSize = true,
                Location = new Point(16, 14)
            };

            lblCronStatus = new Label
            {
                Text = "Status Notifikasi & Sesi 8 Jam: Menyiapkan cronjob...",
                Font = new Font("Segoe UI Semibold", 9.5F, FontStyle.Bold),
                ForeColor = Color.FromArgb(13, 148, 136),
                AutoSize = true,
                Location = new Point(16, 40)
            };

            lblLocalUrl = new Label
            {
                Text = "Akses Laptop / PC  : http://127.0.0.1:" + serverPort,
                Font = new Font("Consolas", 10.0F, FontStyle.Bold),
                ForeColor = Color.FromArgb(15, 23, 42),
                AutoSize = true,
                Location = new Point(16, 68)
            };

            lblLanUrl = new Label
            {
                Text = "Akses Browser HP   : http://" + lanIp + ":" + serverPort + "  (Satu jaringan Wi-Fi / Hotspot)",
                Font = new Font("Consolas", 10.0F, FontStyle.Bold),
                ForeColor = Color.FromArgb(5, 150, 105),
                AutoSize = true,
                Location = new Point(16, 92)
            };

            statusCard.Controls.Add(lblWebStatus);
            statusCard.Controls.Add(lblCronStatus);
            statusCard.Controls.Add(lblLocalUrl);
            statusCard.Controls.Add(lblLanUrl);

            // Action Buttons Row
            FlowLayoutPanel actionPanel = new FlowLayoutPanel
            {
                Location = new Point(18, 222),
                Size = new Size(728, 44),
                Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right,
                FlowDirection = FlowDirection.LeftToRight,
                WrapContents = false,
                BackColor = Color.Transparent
            };

            btnOpenWeb = CreateButton("Buka Web Utama", Color.FromArgb(5, 150, 105), Color.White, 138);
            btnOpenWeb.Click += (s, e) => OpenUrl("http://127.0.0.1:" + serverPort);

            btnOpenLogin = CreateButton("Buka Login Demo", Color.FromArgb(13, 148, 136), Color.White, 136);
            btnOpenLogin.Click += (s, e) => OpenUrl("http://127.0.0.1:" + serverPort + "/login");

            btnTriggerNotif = CreateButton("Sinkronkan Notifikasi Sekarang", Color.FromArgb(217, 119, 6), Color.White, 200);
            btnTriggerNotif.Click += (s, e) => ThreadPool.QueueUserWorkItem(_ => RunCronCommandsOnce(true));

            btnCopyLan = CreateButton("Salin URL HP", Color.FromArgb(2, 132, 199), Color.White, 112);
            btnCopyLan.Click += (s, e) =>
            {
                string url = "http://" + lanIp + ":" + serverPort;
                Clipboard.SetText(url);
                AppendLog("[INFO] URL Akses HP disalin ke clipboard: " + url);
                ShowBalloon("URL Akses HP Disalin", "Tempelkan " + url + " di browser HP yang terhubung ke Wi-Fi/Hotspot yang sama.");
            };

            btnRestart = CreateButton("Restart", Color.FromArgb(71, 85, 105), Color.White, 68);
            btnRestart.Click += (s, e) =>
            {
                ThreadPool.QueueUserWorkItem(_ =>
                {
                    AppendLog("[SISTEM] Me-restart server web dan cronjob notifikasi...");
                    StopAllServices();
                    Thread.Sleep(600);
                    StartAllServices(false);
                });
            };

            btnStop = CreateButton("Stop", Color.FromArgb(225, 29, 72), Color.White, 54);
            btnStop.Click += (s, e) =>
            {
                StopAllServices();
                UpdateStatusLabels(false);
                AppendLog("[SISTEM] Layanan Web Server & Cronjob Notifikasi dihentikan.");
            };

            actionPanel.Controls.Add(btnOpenWeb);
            actionPanel.Controls.Add(btnOpenLogin);
            actionPanel.Controls.Add(btnTriggerNotif);
            actionPanel.Controls.Add(btnCopyLan);
            actionPanel.Controls.Add(btnRestart);
            actionPanel.Controls.Add(btnStop);

            // Log Header Label
            Label lblLogHeader = new Label
            {
                Text = "Log Aktivitas Server Web & Cronjob Notifikasi Real-Time:",
                Font = new Font("Segoe UI Semibold", 9.0F, FontStyle.Bold),
                ForeColor = Color.FromArgb(51, 65, 85),
                AutoSize = true,
                Location = new Point(18, 272)
            };

            // Log Viewer TextBox
            txtLog = new TextBox
            {
                Location = new Point(18, 294),
                Size = new Size(728, 210),
                Anchor = AnchorStyles.Top | AnchorStyles.Bottom | AnchorStyles.Left | AnchorStyles.Right,
                Multiline = true,
                ReadOnly = true,
                ScrollBars = ScrollBars.Vertical,
                BackColor = Color.FromArgb(15, 23, 42),
                ForeColor = Color.FromArgb(226, 232, 240),
                Font = new Font("Consolas", 9.0F, FontStyle.Regular),
                BorderStyle = BorderStyle.FixedSingle
            };

            this.Controls.Add(headerPanel);
            this.Controls.Add(statusCard);
            this.Controls.Add(actionPanel);
            this.Controls.Add(lblLogHeader);
            this.Controls.Add(txtLog);

            // Periodic timer every 60 seconds as failsafe cron runner in addition to schedule:work
            cronWatchTimer = new System.Windows.Forms.Timer();
            cronWatchTimer.Interval = 60000;
            cronWatchTimer.Tick += (s, e) =>
            {
                if (!isShuttingDown && webServerProcess != null && !webServerProcess.HasExited)
                {
                    ThreadPool.QueueUserWorkItem(_ => RunCronCommandsOnce(false));
                }
            };
            cronWatchTimer.Start();
        }

        private Button CreateButton(string text, Color backColor, Color foreColor, int width)
        {
            Button btn = new Button
            {
                Text = text,
                Width = width,
                Height = 36,
                BackColor = backColor,
                ForeColor = foreColor,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI Semibold", 9.0F, FontStyle.Bold),
                Cursor = Cursors.Hand,
                Margin = new Padding(0, 0, 6, 0)
            };
            btn.FlatAppearance.BorderSize = 0;
            return btn;
        }

        private void InitializeTrayIcon()
        {
            trayIcon = new NotifyIcon
            {
                Icon = SystemIcons.Shield,
                Text = "SiTernak-OK Server & Notifikasi",
                Visible = true
            };
            trayIcon.DoubleClick += (s, e) =>
            {
                this.Show();
                this.WindowState = FormWindowState.Normal;
                this.Activate();
            };
        }

        private void ShowBalloon(string title, string body)
        {
            if (trayIcon == null) return;
            try
            {
                trayIcon.BalloonTipTitle = title;
                trayIcon.BalloonTipText = body;
                trayIcon.BalloonTipIcon = ToolTipIcon.Info;
                trayIcon.ShowBalloonTip(4000);
            }
            catch { }
        }

        public void StartAllServices(bool openBrowserWhenReady)
        {
            AppendLog("==================================================================");
            AppendLog(" Memulai SiTernak-OK (Web Server + Engine Notifikasi 8 Jam)");
            AppendLog(" Lokasi Proyek : " + projectRoot);
            AppendLog(" Runtime PHP   : " + (string.IsNullOrEmpty(phpPath) ? "TIDAK DITEMUKAN" : phpPath));
            AppendLog("==================================================================");

            if (string.IsNullOrEmpty(phpPath) || !File.Exists(phpPath))
            {
                AppendLog("[ERROR] File php.exe tidak ditemukan. Pastikan XAMPP / Laragon / PHP terinstal.");
                UpdateStatusLabels(false);
                return;
            }

            if (!File.Exists(Path.Combine(projectRoot, "artisan")))
            {
                AppendLog("[ERROR] File artisan tidak ditemukan di folder: " + projectRoot);
                UpdateStatusLabels(false);
                return;
            }

            EnsureEnvironmentAndDatabase();

            // 1. Run initial cron & notification sync right away
            RunCronCommandsOnce(false);

            // 2. Start Laravel Web Server on 0.0.0.0:8000 (or next free port if occupied by another app)
            StartWebServer();

            // 3. Start Laravel Scheduler Worker (php artisan schedule:work)
            StartSchedulerWorker();

            UpdateStatusLabels(true);

            // Wait briefly for port to listen, then open browser
            if (openBrowserWhenReady && !browserOpenedOnce)
            {
                browserOpenedOnce = true;
                for (int i = 0; i < 25; i++)
                {
                    if (IsPortListening("127.0.0.1", serverPort)) break;
                    Thread.Sleep(200);
                }
                string url = "http://127.0.0.1:" + serverPort;
                AppendLog("[BROWSER] Membuka peramban otomatis ke " + url);
                ShowBalloon(
                    "SiTernak-OK Aktif",
                    "Server Web & Notifikasi 8 Jam berjalan di " + url + " (Akses HP: http://" + lanIp + ":" + serverPort + ")"
                );
                OpenUrl(url);
            }
        }

        private void EnsureEnvironmentAndDatabase()
        {
            try
            {
                string envPath = Path.Combine(projectRoot, ".env");
                string envExamplePath = Path.Combine(projectRoot, ".env.example");
                if (!File.Exists(envPath) && File.Exists(envExamplePath))
                {
                    File.Copy(envExamplePath, envPath);
                    AppendLog("[PERSIAPAN] Membuat file .env dari .env.example...");
                    RunArtisanSync("key:generate --force");
                }

                string sqlitePath = Path.Combine(projectRoot, "database", "database.sqlite");
                if (!File.Exists(sqlitePath))
                {
                    Directory.CreateDirectory(Path.GetDirectoryName(sqlitePath));
                    File.WriteAllBytes(sqlitePath, new byte[0]);
                    AppendLog("[DATABASE] Membuat database SQLite baru & menjalankan migrasi + seeder...");
                    RunArtisanSync("migrate --seed --force");
                }
                else
                {
                    FileInfo fi = new FileInfo(sqlitePath);
                    if (fi.Length == 0)
                    {
                        AppendLog("[DATABASE] Mengisi tabel database SQLite awal (migrate --seed)...");
                        RunArtisanSync("migrate --seed --force");
                    }
                }
            }
            catch (Exception ex)
            {
                AppendLog("[PERINGATAN] Pemeriksaan awal database: " + ex.Message);
            }
        }

        private void StartWebServer()
        {
            if (webServerProcess != null && !webServerProcess.HasExited)
            {
                return;
            }

            string args = string.Format("artisan serve --host=0.0.0.0 --port={0}", serverPort);
            webServerProcess = CreateBackgroundPhpProcess(args, "[WEB]");
            webServerProcess.Start();
            webServerProcess.BeginOutputReadLine();
            webServerProcess.BeginErrorReadLine();
            AppendLog(string.Format("[WEB] Server dijalankan pada http://127.0.0.1:{0} dan http://{1}:{0}", serverPort, lanIp));
        }

        private void StartSchedulerWorker()
        {
            if (schedulerProcess != null && !schedulerProcess.HasExited)
            {
                return;
            }

            schedulerProcess = CreateBackgroundPhpProcess("artisan schedule:work", "[CRON]");
            schedulerProcess.Start();
            schedulerProcess.BeginOutputReadLine();
            schedulerProcess.BeginErrorReadLine();
            AppendLog("[CRON] Worker penjadwalan otomatis (schedule:work) aktif.");
        }

        public void RunCronCommandsOnce(bool showToast)
        {
            try
            {
                string out1 = RunArtisanSync("ternak:operator-session-cron");
                if (!string.IsNullOrEmpty(out1))
                {
                    AppendLog("[NOTIF-8JAM] " + out1.Trim());
                }

                string out2 = RunArtisanSync("ternak:update-pregnancy-status");
                if (!string.IsNullOrEmpty(out2))
                {
                    AppendLog("[SIKLUS-SAPI] " + out2.Trim());
                }

                if (showToast)
                {
                    ShowBalloon(
                        "Sinkronisasi Notifikasi Selesai",
                        string.IsNullOrEmpty(out1) ? "Cronjob 8 jam operator & milestone sapi telah diperbarui." : out1.Trim()
                    );
                }
            }
            catch (Exception ex)
            {
                AppendLog("[CRON-ERROR] " + ex.Message);
            }
        }

        private string RunArtisanSync(string artisanArgs)
        {
            ProcessStartInfo psi = new ProcessStartInfo
            {
                FileName = phpPath,
                Arguments = "artisan " + artisanArgs,
                WorkingDirectory = projectRoot,
                UseShellExecute = false,
                RedirectStandardOutput = true,
                RedirectStandardError = true,
                CreateNoWindow = true,
                StandardOutputEncoding = Encoding.UTF8,
                StandardErrorEncoding = Encoding.UTF8
            };

            using (Process p = Process.Start(psi))
            {
                string output = p.StandardOutput.ReadToEnd();
                string err = p.StandardError.ReadToEnd();
                p.WaitForExit(20000);
                if (!string.IsNullOrWhiteSpace(err))
                {
                    return output + " " + err;
                }
                return output;
            }
        }

        private Process CreateBackgroundPhpProcess(string arguments, string prefix)
        {
            ProcessStartInfo psi = new ProcessStartInfo
            {
                FileName = phpPath,
                Arguments = arguments,
                WorkingDirectory = projectRoot,
                UseShellExecute = false,
                RedirectStandardOutput = true,
                RedirectStandardError = true,
                CreateNoWindow = true,
                StandardOutputEncoding = Encoding.UTF8,
                StandardErrorEncoding = Encoding.UTF8
            };

            Process proc = new Process { StartInfo = psi, EnableRaisingEvents = true };
            proc.OutputDataReceived += (s, e) =>
            {
                if (!string.IsNullOrWhiteSpace(e.Data))
                {
                    AppendLog(prefix + " " + e.Data);
                }
            };
            proc.ErrorDataReceived += (s, e) =>
            {
                if (!string.IsNullOrWhiteSpace(e.Data))
                {
                    AppendLog(prefix + " " + e.Data);
                }
            };
            return proc;
        }

        public void StopAllServices()
        {
            KillProcessSafe(ref webServerProcess);
            KillProcessSafe(ref schedulerProcess);
        }

        private void KillProcessSafe(ref Process proc)
        {
            if (proc == null) return;
            try
            {
                if (!proc.HasExited)
                {
                    KillProcessAndChildren(proc.Id);
                }
            }
            catch { }
            finally
            {
                try { proc.Dispose(); } catch { }
                proc = null;
            }
        }

        private void KillProcessAndChildren(int pid)
        {
            try
            {
                ProcessStartInfo psi = new ProcessStartInfo
                {
                    FileName = "taskkill",
                    Arguments = "/F /T /PID " + pid,
                    UseShellExecute = false,
                    CreateNoWindow = true,
                    RedirectStandardOutput = true,
                    RedirectStandardError = true
                };
                using (Process killer = Process.Start(psi))
                {
                    killer.WaitForExit(3000);
                }
            }
            catch { }
        }

        private void UpdateStatusLabels(bool running)
        {
            if (this.InvokeRequired)
            {
                this.BeginInvoke(new Action<bool>(UpdateStatusLabels), running);
                return;
            }

            if (running)
            {
                lblWebStatus.Text = "Status Web Server: AKTIF (Siap Diakses di Laptop & HP)";
                lblWebStatus.ForeColor = Color.FromArgb(5, 150, 105);
                lblCronStatus.Text = "Status Notifikasi & Sesi 8 Jam: AKTIF (Cronjob Berjalan Otomatis Setiap Menit)";
                lblCronStatus.ForeColor = Color.FromArgb(13, 148, 136);
            }
            else
            {
                lblWebStatus.Text = "Status Web Server: BERHENTI";
                lblWebStatus.ForeColor = Color.FromArgb(225, 29, 72);
                lblCronStatus.Text = "Status Notifikasi & Sesi 8 Jam: BERHENTI";
                lblCronStatus.ForeColor = Color.FromArgb(100, 116, 139);
            }
        }

        private void AppendLog(string line)
        {
            if (isShuttingDown || txtLog == null || txtLog.IsDisposed) return;

            if (txtLog.InvokeRequired)
            {
                try
                {
                    txtLog.BeginInvoke(new Action<string>(AppendLog), line);
                }
                catch { }
                return;
            }

            string timestamp = DateTime.Now.ToString("HH:mm:ss");
            txtLog.AppendText("[" + timestamp + "] " + line + Environment.NewLine);
        }

        private void OpenUrl(string url)
        {
            try
            {
                Process.Start(new ProcessStartInfo
                {
                    FileName = url,
                    UseShellExecute = true
                });
            }
            catch (Exception ex)
            {
                AppendLog("[ERROR] Gagal membuka browser: " + ex.Message);
            }
        }

        private bool IsPortListening(string host, int port)
        {
            try
            {
                using (TcpClient client = new TcpClient())
                {
                    IAsyncResult result = client.BeginConnect(host, port, null, null);
                    bool success = result.AsyncWaitHandle.WaitOne(TimeSpan.FromMilliseconds(180));
                    if (!success) return false;
                    client.EndConnect(result);
                    return true;
                }
            }
            catch
            {
                return false;
            }
        }

        private static string DetectProjectRoot()
        {
            string exeDir = AppDomain.CurrentDomain.BaseDirectory;
            if (File.Exists(Path.Combine(exeDir, "artisan")))
            {
                return exeDir.TrimEnd('\\', '/');
            }

            string parent = Directory.GetParent(exeDir.TrimEnd('\\', '/')) != null
                ? Directory.GetParent(exeDir.TrimEnd('\\', '/')).FullName
                : exeDir;
            if (File.Exists(Path.Combine(parent, "artisan")))
            {
                return parent;
            }

            return Directory.GetCurrentDirectory();
        }

        private static string DetectPhpExecutable()
        {
            // 1. Check standard locations first
            string[] candidates = new string[]
            {
                @"C:\xampp\php\php.exe",
                @"D:\xampp\php\php.exe",
                @"C:\laragon\bin\php\php.exe"
            };

            foreach (string c in candidates)
            {
                if (File.Exists(c)) return c;
            }

            // 2. Search PATH environment variable
            string pathEnv = Environment.GetEnvironmentVariable("PATH") ?? "";
            foreach (string dir in pathEnv.Split(Path.PathSeparator))
            {
                try
                {
                    if (string.IsNullOrWhiteSpace(dir)) continue;
                    string candidate = Path.Combine(dir.Trim(), "php.exe");
                    if (File.Exists(candidate)) return candidate;
                }
                catch { }
            }

            // 3. Search Laragon PHP subdirectories if installed
            string[] laragonRoots = new string[] { @"C:\laragon\bin\php", @"D:\laragon\bin\php" };
            foreach (string root in laragonRoots)
            {
                if (Directory.Exists(root))
                {
                    string[] found = Directory.GetFiles(root, "php.exe", SearchOption.AllDirectories);
                    if (found.Length > 0) return found[0];
                }
            }

            return "php.exe";
        }

        private static string DetectLocalLanIp()
        {
            try
            {
                foreach (NetworkInterface ni in NetworkInterface.GetAllNetworkInterfaces())
                {
                    if (ni.OperationalStatus != OperationalStatus.Up) continue;
                    if (ni.NetworkInterfaceType == NetworkInterfaceType.Loopback) continue;

                    IPInterfaceProperties props = ni.GetIPProperties();
                    foreach (UnicastIPAddressInformation ip in props.UnicastAddresses)
                    {
                        if (ip.Address.AddressFamily == AddressFamily.InterNetwork && !IPAddress.IsLoopback(ip.Address))
                        {
                            string addr = ip.Address.ToString();
                            if (!addr.StartsWith("169.254."))
                            {
                                return addr;
                            }
                        }
                    }
                }
            }
            catch { }
            return "127.0.0.1";
        }
    }

    public static class Program
    {
        [STAThread]
        public static int Main(string[] args)
        {
            if (args != null && args.Length > 0 && args[0] == "--check")
            {
                return 0;
            }

            if (args != null && args.Length > 0 && args[0] == "--self-test")
            {
                MainForm form = new MainForm(false);
                form.StartAllServices(false);
                Thread.Sleep(2500);
                form.StopAllServices();
                return 0;
            }

            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            Application.Run(new MainForm(true));
            return 0;
        }
    }
}
