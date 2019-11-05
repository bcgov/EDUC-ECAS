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
                SolutionName = 'ECASCustomizations'
                Managed = $false
                ZipFile = "$projectRoot\temp\export\ECASCustomizations.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'ECASCustomizations'
                Managed = $true
                ZipFile = "$projectRoot\temp\export\ECASCustomizations_managed.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'ECASProcesses'
                Managed = $false
                ZipFile = "$projectRoot\temp\export\ECASProcesses.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'ECASProcesses'
                Managed = $true
                ZipFile = "$projectRoot\temp\export\ECASProcesses_managed.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'ECASSiteMapandApps'
                Managed = $false
                ZipFile = "$projectRoot\temp\export\ECASSiteMapandApps.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'ECASSiteMapandApps'
                Managed = $true
                ZipFile = "$projectRoot\temp\export\ECASSiteMapandApps_managed.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'BCCASAPIntegration'
                Managed = $false
                ZipFile = "$projectRoot\temp\export\BCCASAPIntegration.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'BCCASAPIntegration'
                Managed = $true
                ZipFile = "$projectRoot\temp\export\BCCASAPIntegration_managed.zip"
            }
        )
    }
    ExtractSolutions = @(
        [PSCustomObject]@{
            ZipFile = "$projectRoot\temp\export\ECASCustomizations.zip"
            PackageType = 'Both'
            Folder = "$projectRoot\data\solutions\ECASCustomizations"
        },
        [PSCustomObject]@{
            ZipFile = "$projectRoot\temp\export\ECASProcesses.zip"
            PackageType = 'Both'
            Folder = "$projectRoot\data\solutions\ECASProcesses"
        },
        [PSCustomObject]@{
            ZipFile = "$projectRoot\temp\export\ECASSiteMapandApps.zip"
            PackageType = 'Both'
            Folder = "$projectRoot\data\solutions\ECASSiteMapandApps"
        },
        [PSCustomObject]@{
            ZipFile = "$projectRoot\temp\export\BCCASAPIntegration.zip"
            PackageType = 'Both'
            Folder = "$projectRoot\data\solutions\BCCASAPIntegration"
        }
    )
}