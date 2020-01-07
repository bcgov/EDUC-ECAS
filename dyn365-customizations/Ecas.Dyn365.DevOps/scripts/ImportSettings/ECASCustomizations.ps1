param (
    [Parameter(Mandatory)]
    [ValidateNotNullOrEmpty()]
    [hashtable]
    $CrmConnectionParameters,

    [ValidateSet("Managed","Unmanaged")]
    [string]
    $PackageType = "Unmanaged"
)

$projectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
$solutionExt = if($PackageType -eq "Managed") { "_managed" }

@{

    ExtractedSolutions = @(
        [PSCustomObject]@{
            Folder = "$projectRoot\data\solutions\ECASCustomizations"
            PackageType = $PackageType
            ZipFile = "$projectRoot\temp\packed\ECASCustomizations$solutionExt.zip"
        },
        [PSCustomObject]@{
            Folder = "$projectRoot\data\solutions\ECASProcesses"
            PackageType = $PackageType
            ZipFile = "$projectRoot\temp\packed\ECASProcesses$solutionExt.zip"
        },
        [PSCustomObject]@{
            Folder = "$projectRoot\data\solutions\ECASSiteMapandApps"
            PackageType = $PackageType
            ZipFile = "$projectRoot\temp\packed\ECASSiteMapandApps$solutionExt.zip"
        },
        [PSCustomObject]@{
            Folder = "$projectRoot\data\solutions\BCCASAPIntegration"
            PackageType = $PackageType
            ZipFile = "$projectRoot\temp\packed\BCCASAPIntegration$solutionExt.zip"
        }
    )
    CrmPackageDefinition = @(
        [PSCustomObject]@{
            SolutionZipFiles = @(
                "$projectRoot\temp\export\ECASCustomizations$solutionExt.zip",
                "$projectRoot\temp\export\ECASProcesses$solutionExt.zip",
                "$projectRoot\temp\export\ECASSiteMapandApps$solutionExt.zip",
		        "$projectRoot\temp\export\BCCASAPIntegration$solutionExt.zip"
            )
        }
    )
    CrmOrganizationProvisionDefinition = [PSCustomObject]@{
        ComputerName = $CrmConnectionParameters.ServerUrl.Replace("http://", "")
        Credential = $CrmConnectionParameters.Credential
        OrganizationName = $CrmConnectionParameters.OrganizationName
        SqlBackupFile = 'C:\Program Files\Microsoft SQL Server\MSSQL13.MSSQLSERVER\MSSQL\Backup\new_MSCRM.bak'
        SqlDataFile = "C:\Program Files\Microsoft SQL Server\MSSQL13.MSSQLSERVER\MSSQL\DATA\$($CrmConnectionParameters.OrganizationName)_MSCRM.mdf"
        SqlLogFile = "C:\Program Files\Microsoft SQL Server\MSSQL13.MSSQLSERVER\MSSQL\DATA\$($CrmConnectionParameters.OrganizationName)_MSCRM_log.ldf"
    }
    CrmPackageDeploymentDefinition = [PSCustomObject]@{
        CrmConnectionParameters = $CrmConnectionParameters
    }
}