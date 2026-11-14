@echo off
chcp 65001 >nul
echo Mengompilasi SiTernak-OK.exe...
"C:\Windows\Microsoft.NET\Framework64\v4.0.30319\csc.exe" /target:winexe /out:"%~dp0..\SiTernak-OK.exe" /r:System.Windows.Forms.dll /r:System.Drawing.dll "%~dp0SiTernakLauncher.cs"
if %ERRORLEVEL% EQU 0 (
    echo [SUKSES] File SiTernak-OK.exe berhasil dibuat di folder utama proyek.
) else (
    echo [GAGAL] Terjadi kesalahan saat kompilasi.
)
