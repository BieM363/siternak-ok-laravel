using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.Net;
using System.Net.NetworkInformation;
using System.Net.Sockets;
using System.Text;
using System.Text.RegularExpressions;
using System.Threading;
using System.Windows.Forms;

namespace SiTernakLauncher
{
    public class MainForm : Form
    {
        private string projectRoot;
        private string phpPath;
        private string cloudflaredPath;
        private int serverPort = 8000;
        private string lanIp = "127.0.0.1";
        private string cloudflareUrl = "";

        private Process webServerProcess;
        private Process schedulerProcess;
        private Process cloudflareProcess;
        private System.Windows.Forms.Timer cronWatchTimer;
        private bool isShuttingDown = false;
        private bool browserOpenedOnce = false;

        private static readonly Regex TryCloudflareRegex = new Regex(
            @"https://[a-zA-Z0-9\-]+\.trycloudflare\.com",
            RegexOptions.Compiled | RegexOptions.IgnoreCase
        );

        // UI Controls
        private Label lblWebStatus;
        private Label lblCronStatus;
        private Label lblCloudflareStatus;
        private Label lblLocalUrl;
        private TextBox txtCloudflareUrl;
        private PictureBox picQrCode;
        private Label lblQrCaption;
        private TextBox txtLog;
        private Button btnOpenWeb;
        private Button btnOpenCloudflare;
        private Button btnCopyCloudflare;
        private Button btnTriggerNotif;
        private Button btnRestart;
        private Button btnStop;
        private NotifyIcon trayIcon;

        public MainForm(bool autoOpenBrowser)
        {
            ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12;
            this.projectRoot = DetectProjectRoot();
            this.phpPath = DetectPhpExecutable();
            this.cloudflaredPath = DetectCloudflaredExecutable(this.projectRoot);
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
            this.Text = "SiTernak-OK — Peluncur Web, Cloudflare Tunnel & Notifikasi 8 Jam";
            this.Size = new Size(860, 620);
            this.MinimumSize = new Size(820, 580);
            this.StartPosition = FormStartPosition.CenterScreen;
            this.BackColor = Color.FromArgb(241, 245, 249);
            this.Font = new Font("Segoe UI", 9.5F, FontStyle.Regular);
            this.Icon = SystemIcons.Shield;

            // Header Banner Panel
            Panel headerPanel = new Panel
            {
                Dock = DockStyle.Top,
                Height = 76,
                BackColor = Color.FromArgb(15, 23, 42),
                Padding = new Padding(20, 14, 20, 14)
            };

            Label lblTitle = new Label
            {
                Text = "SiTernak-OK — Server Web, Cloudflare HTTPS & Cronjob Notifikasi 8 Jam",
                ForeColor = Color.White,
                Font = new Font("Segoe UI Semibold", 13.0F, FontStyle.Bold),
                AutoSize = true,
                Location = new Point(20, 14)
            };

            Label lblSubtitle = new Label
            {
                Text = "Dinas Peternakan dan Perkebunan Provinsi Gorontalo • Akses Langsung Laptop & Browser HP via Cloudflare Tunnel",
                ForeColor = Color.FromArgb(16, 185, 129),
                Font = new Font("Segoe UI", 9.0F, FontStyle.Regular),
                AutoSize = true,
                Location = new Point(20, 44)
            };

            headerPanel.Controls.Add(lblTitle);
            headerPanel.Controls.Add(lblSubtitle);

            // Status & QR Card Panel
            Panel statusCard = new Panel
            {
                Location = new Point(18, 88),
                Size = new Size(808, 176),
                Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right,
                BackColor = Color.White,
                BorderStyle = BorderStyle.FixedSingle
            };

            lblWebStatus = new Label
            {
                Text = "Status Web Server        : Menyiapkan layanan...",
                Font = new Font("Segoe UI Semibold", 9.75F, FontStyle.Bold),
                ForeColor = Color.FromArgb(217, 119, 6),
                AutoSize = true,
                Location = new Point(16, 14)
            };

            lblCronStatus = new Label
            {
                Text = "Status Cron Notifikasi   : Menyiapkan cronjob sesi 8 jam & pengingat 3 hari...",
                Font = new Font("Segoe UI Semibold", 9.5F, FontStyle.Bold),
                ForeColor = Color.FromArgb(13, 148, 136),
                AutoSize = true,
                Location = new Point(16, 38)
            };

            lblCloudflareStatus = new Label
            {
                Text = "Status Cloudflare Tunnel : Menghubungkan ke jaringan Cloudflare HTTPS...",
                Font = new Font("Segoe UI Semibold", 9.75F, FontStyle.Bold),
                ForeColor = Color.FromArgb(2, 132, 199),
                AutoSize = true,
                Location = new Point(16, 62)
            };

            lblLocalUrl = new Label
            {
                Text = "URL Laptop (Lokal)       : http://127.0.0.1:" + serverPort + "   |   LAN: http://" + lanIp + ":" + serverPort,
                Font = new Font("Consolas", 9.75F, FontStyle.Regular),
                ForeColor = Color.FromArgb(51, 65, 85),
                AutoSize = true,
                Location = new Point(16, 90)
            };

            Label lblCfPrompt = new Label
            {
                Text = "Link Publik HP (HTTPS) :",
                Font = new Font("Segoe UI Semibold", 9.75F, FontStyle.Bold),
                ForeColor = Color.FromArgb(5, 150, 105),
                AutoSize = true,
                Location = new Point(16, 122)
            };

            txtCloudflareUrl = new TextBox
            {
                Text = "Sedang membuat link HTTPS *.trycloudflare.com (tunggu 3-8 detik)...",
                Font = new Font("Consolas", 10.5F, FontStyle.Bold),
                ForeColor = Color.FromArgb(5, 150, 105),
                BackColor = Color.FromArgb(240, 253, 244),
                ReadOnly = true,
                BorderStyle = BorderStyle.FixedSingle,
                Location = new Point(182, 119),
                Size = new Size(445, 28)
            };

            Label lblHint = new Label
            {
                Text = "Buka link HTTPS di atas dari browser HP mana saja (Wi-Fi / Data Seluler) atau scan QR di kanan.",
                Font = new Font("Segoe UI", 8.5F, FontStyle.Regular),
                ForeColor = Color.FromArgb(100, 116, 139),
                AutoSize = true,
                Location = new Point(16, 150)
            };

            // QR Code Box on Right Side of Status Card
            picQrCode = new PictureBox
            {
                Location = new Point(650, 10),
                Size = new Size(136, 136),
                BorderStyle = BorderStyle.FixedSingle,
                BackColor = Color.FromArgb(248, 250, 252),
                SizeMode = PictureBoxSizeMode.StretchImage
            };

            lblQrCaption = new Label
            {
                Text = "Menunggu QR HP...",
                Font = new Font("Segoe UI Semibold", 8.25F, FontStyle.Bold),
                ForeColor = Color.FromArgb(71, 85, 105),
                AutoSize = false,
                TextAlign = ContentAlignment.MiddleCenter,
                Location = new Point(642, 149),
                Size = new Size(152, 20)
            };

            statusCard.Controls.Add(lblWebStatus);
            statusCard.Controls.Add(lblCronStatus);
            statusCard.Controls.Add(lblCloudflareStatus);
            statusCard.Controls.Add(lblLocalUrl);
            statusCard.Controls.Add(lblCfPrompt);
            statusCard.Controls.Add(txtCloudflareUrl);
            statusCard.Controls.Add(lblHint);
            statusCard.Controls.Add(picQrCode);
            statusCard.Controls.Add(lblQrCaption);

            // Action Buttons Row
            FlowLayoutPanel actionPanel = new FlowLayoutPanel
            {
                Location = new Point(18, 274),
                Size = new Size(808, 44),
                Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right,
                FlowDirection = FlowDirection.LeftToRight,
                WrapContents = false,
                BackColor = Color.Transparent
            };

            btnOpenCloudflare = CreateButton("Buka Link Cloudflare (HTTPS)", Color.FromArgb(5, 150, 105), Color.White, 196);
            btnOpenCloudflare.Click += (s, e) =>
            {
                if (!string.IsNullOrEmpty(cloudflareUrl))
                {
                    OpenUrl(cloudflareUrl);
                }
                else
                {
                    OpenUrl("http://127.0.0.1:" + serverPort);
                }
            };

            btnCopyCloudflare = CreateButton("Salin Link Cloudflare HP", Color.FromArgb(2, 132, 199), Color.White, 168);
            btnCopyCloudflare.Click += (s, e) =>
            {
                string target = !string.IsNullOrEmpty(cloudflareUrl)
                    ? cloudflareUrl
                    : ("http://127.0.0.1:" + serverPort);
                Clipboard.SetText(target);
                AppendLog("[CLIPBOARD] Link berhasil disalin: " + target);
                ShowBalloon("Link Disalin", "Tempelkan " + target + " di browser HP Anda.");
            };

            btnOpenWeb = CreateButton("Buka Localhost", Color.FromArgb(13, 148, 136), Color.White, 118);
            btnOpenWeb.Click += (s, e) => OpenUrl("http://127.0.0.1:" + serverPort);

            btnTriggerNotif = CreateButton("Sinkronkan Notifikasi", Color.FromArgb(217, 119, 6), Color.White, 158);
            btnTriggerNotif.Click += (s, e) => ThreadPool.QueueUserWorkItem(_ => RunCronCommandsOnce(true));

            btnRestart = CreateButton("Restart", Color.FromArgb(71, 85, 105), Color.White, 72);
            btnRestart.Click += (s, e) =>
            {
                ThreadPool.QueueUserWorkItem(_ =>
                {
                    AppendLog("[SISTEM] Me-restart Web Server, Cloudflare Tunnel & Cronjob...");
                    StopAllServices();
                    Thread.Sleep(600);
                    StartAllServices(false);
                });
            };

            btnStop = CreateButton("Stop", Color.FromArgb(225, 29, 72), Color.White, 60);
            btnStop.Click += (s, e) =>
            {
                StopAllServices();
                UpdateStatusLabels(false);
                AppendLog("[SISTEM] Seluruh layanan dihentikan.");
            };

            actionPanel.Controls.Add(btnOpenCloudflare);
            actionPanel.Controls.Add(btnCopyCloudflare);
            actionPanel.Controls.Add(btnOpenWeb);
            actionPanel.Controls.Add(btnTriggerNotif);
            actionPanel.Controls.Add(btnRestart);
            actionPanel.Controls.Add(btnStop);

            // Log Header Label
            Label lblLogHeader = new Label
            {
                Text = "Log Aktivitas Web Server, Cloudflare Tunnel & Notifikasi 8 Jam Real-Time:",
                Font = new Font("Segoe UI Semibold", 9.0F, FontStyle.Bold),
                ForeColor = Color.FromArgb(51, 65, 85),
                AutoSize = true,
                Location = new Point(18, 324)
            };

            // Log Viewer TextBox
            txtLog = new TextBox
            {
                Location = new Point(18, 346),
                Size = new Size(808, 220),
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

            // Periodic timer every 60 seconds for cron sync
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
                Text = "SiTernak-OK (Web + Cloudflare + Notifikasi)",
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
            AppendLog("======================================================================");
            AppendLog(" Memulai SiTernak-OK (Web Server + Cloudflare Tunnel + Notifikasi 8 Jam)");
            AppendLog(" Folder Proyek : " + projectRoot);
            AppendLog(" Runtime PHP   : " + (string.IsNullOrEmpty(phpPath) ? "TIDAK DITEMUKAN" : phpPath));
            AppendLog("======================================================================");

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

            // 1. Run initial cron & notification sync
            RunCronCommandsOnce(false);

            // 2. Start Laravel Web Server on 0.0.0.0:8000
            StartWebServer();

            // 3. Start Laravel Scheduler Worker
            StartSchedulerWorker();

            UpdateStatusLabels(true);

            // Wait for local web server port 8000 to listen
            for (int i = 0; i < 25; i++)
            {
                if (IsPortListening("127.0.0.1", serverPort)) break;
                Thread.Sleep(200);
            }

            // 4. Start Cloudflare Quick Tunnel pointing to http://127.0.0.1:8000
            StartCloudflareTunnel();

            if (openBrowserWhenReady && !browserOpenedOnce)
            {
                browserOpenedOnce = true;
                string url = "http://127.0.0.1:" + serverPort;
                AppendLog("[BROWSER] Membuka peramban lokal ke " + url);
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
            webServerProcess = CreateBackgroundProcess(phpPath, args, "[WEB]", null);
            webServerProcess.Start();
            webServerProcess.BeginOutputReadLine();
            webServerProcess.BeginErrorReadLine();
            AppendLog(string.Format("[WEB] Server lokal aktif di http://127.0.0.1:{0}", serverPort));
        }

        private void StartSchedulerWorker()
        {
            if (schedulerProcess != null && !schedulerProcess.HasExited)
            {
                return;
            }

            schedulerProcess = CreateBackgroundProcess(phpPath, "artisan schedule:work", "[CRON]", null);
            schedulerProcess.Start();
            schedulerProcess.BeginOutputReadLine();
            schedulerProcess.BeginErrorReadLine();
            AppendLog("[CRON] Worker penjadwalan otomatis (schedule:work) aktif.");
        }

        private void StartCloudflareTunnel()
        {
            if (cloudflareProcess != null && !cloudflareProcess.HasExited)
            {
                return;
            }

            this.cloudflaredPath = DetectCloudflaredExecutable(this.projectRoot);
            if (string.IsNullOrEmpty(this.cloudflaredPath) || !File.Exists(this.cloudflaredPath))
            {
                AppendLog("[CLOUDFLARE] Mengunduh cloudflared.exe resmi dari GitHub...");
                UpdateCloudflareUi("", "Mengunduh komponen Cloudflare Tunnel...");
                string targetPath = Path.Combine(projectRoot, "launcher", "cloudflared.exe");
                try
                {
                    Directory.CreateDirectory(Path.GetDirectoryName(targetPath));
                    using (WebClient wc = new WebClient())
                    {
                        wc.DownloadFile(
                            "https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.exe",
                            targetPath
                        );
                    }
                    this.cloudflaredPath = targetPath;
                    AppendLog("[CLOUDFLARE] Unduhan cloudflared.exe selesai.");
                }
                catch (Exception ex)
                {
                    AppendLog("[CLOUDFLARE-ERROR] Gagal mengunduh cloudflared.exe: " + ex.Message);
                    UpdateCloudflareUi("", "Gagal mengunduh cloudflared.exe");
                    return;
                }
            }

            cloudflareUrl = "";
            UpdateCloudflareUi("", "Menghubungkan tunnel HTTPS ke Cloudflare...");

            string cfArgs = string.Format("tunnel --url http://127.0.0.1:{0} --no-autoupdate", serverPort);
            cloudflareProcess = CreateBackgroundProcess(this.cloudflaredPath, cfArgs, "[CF]", OnCloudflareOutputLine);
            cloudflareProcess.Start();
            cloudflareProcess.BeginOutputReadLine();
            cloudflareProcess.BeginErrorReadLine();
            AppendLog("[CLOUDFLARE] Menjalankan Cloudflare Quick Tunnel untuk http://127.0.0.1:" + serverPort + " ...");
        }

        private void OnCloudflareOutputLine(string line)
        {
            if (string.IsNullOrEmpty(line)) return;

            Match match = TryCloudflareRegex.Match(line);
            if (match.Success)
            {
                string detectedUrl = match.Value.Trim();
                if ( !string.Equals(cloudflareUrl, detectedUrl, StringComparison.OrdinalIgnoreCase))
                {
                    cloudflareUrl = detectedUrl;
                    AppendLog("======================================================================");
                    AppendLog(" [CLOUDFLARE SIAP] Link Akses HP & Publik (HTTPS): " + cloudflareUrl);
                    AppendLog("======================================================================");
                    UpdateCloudflareUi(cloudflareUrl, "AKTIF — Tunnel HTTPS Siap Diakses dari HP Mana Saja!");
                    ShowBalloon(
                        "Link Cloudflare HP Siap!",
                        "URL " + cloudflareUrl + " telah disalin ke clipboard. Buka di HP atau scan QR Code!"
                    );
                }
            }
        }

        private void UpdateCloudflareUi(string url, string statusText)
        {
            if (this.InvokeRequired)
            {
                this.BeginInvoke(new Action<string, string>(UpdateCloudflareUi), url, statusText);
                return;
            }

            lblCloudflareStatus.Text = "Status Cloudflare Tunnel : " + statusText;
            if (!string.IsNullOrEmpty(url))
            {
                lblCloudflareStatus.ForeColor = Color.FromArgb(5, 150, 105);
                txtCloudflareUrl.Text = url;
                lblQrCaption.Text = "Scan QR dari Kamera HP";
                try
                {
                    Clipboard.SetText(url);
                }
                catch { }

                try
                {
                    string qrApi = "https://api.qrserver.com/v1/create-qr-code/?size=160x160&margin=6&data=" + Uri.EscapeDataString(url);
                    picQrCode.LoadAsync(qrApi);
                }
                catch { }
            }
            else
            {
                lblCloudflareStatus.ForeColor = Color.FromArgb(2, 132, 199);
            }
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

        private Process CreateBackgroundProcess(string exePath, string arguments, string prefix, Action<string> lineHook)
        {
            ProcessStartInfo psi = new ProcessStartInfo
            {
                FileName = exePath,
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
                    if (lineHook != null) lineHook(e.Data);
                    AppendLog(prefix + " " + e.Data);
                }
            };
            proc.ErrorDataReceived += (s, e) =>
            {
                if (!string.IsNullOrWhiteSpace(e.Data))
                {
                    if (lineHook != null) lineHook(e.Data);
                    AppendLog(prefix + " " + e.Data);
                }
            };
            return proc;
        }

        public void StopAllServices()
        {
            KillProcessSafe(ref cloudflareProcess);
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
                lblWebStatus.Text = "Status Web Server        : AKTIF (http://127.0.0.1:" + serverPort + ")";
                lblWebStatus.ForeColor = Color.FromArgb(5, 150, 105);
                lblCronStatus.Text = "Status Cron Notifikasi   : AKTIF (Otomatis Setiap Menit)";
                lblCronStatus.ForeColor = Color.FromArgb(13, 148, 136);
            }
            else
            {
                lblWebStatus.Text = "Status Web Server        : BERHENTI";
                lblWebStatus.ForeColor = Color.FromArgb(225, 29, 72);
                lblCronStatus.Text = "Status Cron Notifikasi   : BERHENTI";
                lblCronStatus.ForeColor = Color.FromArgb(100, 116, 139);
                lblCloudflareStatus.Text = "Status Cloudflare Tunnel : BERHENTI";
                lblCloudflareStatus.ForeColor = Color.FromArgb(100, 116, 139);
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

        private static string DetectCloudflaredExecutable(string root)
        {
            string[] localCandidates = new string[]
            {
                Path.Combine(root, "launcher", "cloudflared.exe"),
                Path.Combine(root, "cloudflared.exe")
            };

            foreach (string c in localCandidates)
            {
                if (File.Exists(c))
                {
                    FileInfo fi = new FileInfo(c);
                    if (fi.Length > 1000000) return c;
                }
            }

            string pathEnv = Environment.GetEnvironmentVariable("PATH") ?? "";
            foreach (string dir in pathEnv.Split(Path.PathSeparator))
            {
                try
                {
                    if (string.IsNullOrWhiteSpace(dir)) continue;
                    string candidate = Path.Combine(dir.Trim(), "cloudflared.exe");
                    if (File.Exists(candidate)) return candidate;
                }
                catch { }
            }

            return "";
        }

        private static string DetectPhpExecutable()
        {
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
