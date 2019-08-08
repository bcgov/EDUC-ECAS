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
	#ExtractedData = [PSCustomObject]@{
    #    Folder = "$projectRoot\data\LCLBPortalData"
    #    ZipFile = "$projectRoot\temp\packed\data\LCLBPortalData.zip"
    #}
    ExtractedSolutions = @(
        [PSCustomObject]@{
            Folder = "$projectRoot\solutions\LCLBSchemaCustomizations"
            PackageType = $PackageType
            ZipFile = "$projectRoot\temp\packed\LCLBSchemaCustomizations$solutionExt.zip"
        },
        [PSCustomObject]@{
            Folder = "$projectRoot\solutions\LiquorProcesses"
            PackageType = $PackageType
            ZipFile = "$projectRoot\temp\packed\LiquorProcesses$solutionExt.zip"
        },
        [PSCustomObject]@{
            Folder = "$projectRoot\solutions\LCLBSiteMap"
            PackageType = $PackageType
            ZipFile = "$projectRoot\temp\packed\LCLBSiteMap$solutionExt.zip"
        }
    )
    CrmPackageDefinition = @(
        [PSCustomObject]@{
			DataZipFile = "$projectRoot\temp\packed\data\LCLBPortalData.zip"
            SolutionZipFiles = @(                
                "$projectRoot\data\solutions\Adoxio\AdoxioAutoNumber_managed.zip",	
                "$projectRoot\data\solutions\Adoxio\AdoxioDynamicsUI_managed.zip",				
                "$projectRoot\temp\export\LCLBSchemaCustomizations$solutionExt.zip",
                "$projectRoot\temp\export\LiquorProcesses$solutionExt.zip",
                "$projectRoot\temp\export\LCLBSiteMap$solutionExt.zip"
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