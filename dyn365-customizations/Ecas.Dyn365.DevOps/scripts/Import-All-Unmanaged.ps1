Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
[System.Net.ServicePointManager]::SecurityProtocol = [System.Net.SecurityProtocolType]::Tls12
[Environment]::SetEnvironmentVariable("CRM_SDK_PATH", "C:\CRMSDK9_0\Tools")
Import-Module .\Microsoft.Xrm.Data.Powershell\2.8.0\Microsoft.Xrm.Data.PowerShell.psd1
Import-Module .\Adoxio.Dynamics.DevOps\0.8.0\Adoxio.Dynamics.DevOps.psd1
.\Import.ps1 -CrmConnectionName AndrewVMDEV-LCRB -ImportSettings LCLBPortalsCustomizations -PackageType Unmanaged

