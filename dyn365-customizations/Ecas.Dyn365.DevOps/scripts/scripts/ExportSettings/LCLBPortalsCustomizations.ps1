param (
    [Parameter(Mandatory)]
    [ValidateNotNullOrEmpty()]
    [hashtable]
    $CrmConnectionParameters
)

$projectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)

@{
    ExportSolutions = [PSCustomObject]@{
        CrmConnectionParameters = $CrmConnectionParameters
        Solutions = @(
            [PSCustomObject]@{
                SolutionName = 'LCLBSchemaCustomizations'
                Managed = $false
                ZipFile = "$projectRoot\temp\export\LCLBSchemaCustomizations.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'LCLBSchemaCustomizations'
                Managed = $true
                ZipFile = "$projectRoot\temp\export\LCLBSchemaCustomizations_managed.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'SecurityRoles'
                Managed = $false
                ZipFile = "$projectRoot\temp\export\SecurityRoles.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'SecurityRoles'
                Managed = $true
                ZipFile = "$projectRoot\temp\export\SecurityRoles_managed.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'LiquorProcesses'
                Managed = $false
                ZipFile = "$projectRoot\temp\export\LiquorProcesses.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'LiquorProcesses'
                Managed = $true
                ZipFile = "$projectRoot\temp\export\LiquorProcesses_managed.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'LCLBSiteMap'
                Managed = $false
                ZipFile = "$projectRoot\temp\export\LCLBSiteMap.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'LCLBSiteMap'
                Managed = $true
                ZipFile = "$projectRoot\temp\export\LCLBSiteMap_managed.zip"
            }
        )
    }
    ExtractSolutions = @(
        [PSCustomObject]@{
            ZipFile = "$projectRoot\temp\export\LCLBSchemaCustomizations.zip"
            PackageType = 'Both'
            Folder = "$projectRoot\data\solutions\LCLBSchemaCustomizations"
        },
        [PSCustomObject]@{
            ZipFile = "$projectRoot\temp\export\SecurityRoles.zip"
            PackageType = 'Both'
            Folder = "$projectRoot\data\solutions\SecurityRoles"
        },
        [PSCustomObject]@{
            ZipFile = "$projectRoot\temp\export\LiquorProcesses.zip"
            PackageType = 'Both'
            Folder = "$projectRoot\data\solutions\LiquorProcesses"
        },
        [PSCustomObject]@{
            ZipFile = "$projectRoot\temp\export\LCLBSiteMap.zip"
            PackageType = 'Both'
            Folder = "$projectRoot\data\solutions\LCLBSiteMap"
        }
    )
}