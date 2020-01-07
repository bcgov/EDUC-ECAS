Import-Module .\Microsoft.Xrm.Data.Powershell\2.8.0\Microsoft.Xrm.Data.PowerShell.psd1
Import-Module .\Adoxio.Dynamics.DevOps\0.8.0\Adoxio.Dynamics.DevOps.psd1
.\Export.ps1 -CrmConnectionName OnlineDev-LCLBPortal -ExportSettings LCLBPortalsData -Actions Edit-CrmSchemaFile