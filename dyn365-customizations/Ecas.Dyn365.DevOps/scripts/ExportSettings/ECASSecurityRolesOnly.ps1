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
                SolutionName = 'SecurityRoles'
                Managed = $false
                ZipFile = "$projectRoot\temp\export\SecurityRoles.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'SecurityRoles'
                Managed = $true
                ZipFile = "$projectRoot\temp\export\SecurityRoles_managed.zip"
            }
        )
    }
    ExtractSolutions = @(
        [PSCustomObject]@{
            ZipFile = "$projectRoot\temp\export\SecurityRoles.zip"
            PackageType = 'Both'
            Folder = "$projectRoot\data\solutions\SecurityRoles"
        }
    )
}