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
                SolutionName = 'Reports'
                Managed = $false
                ZipFile = "$projectRoot\temp\export\Reports.zip"
            },
            [PSCustomObject]@{
                SolutionName = 'Reports'
                Managed = $true
                ZipFile = "$projectRoot\temp\export\Reports_managed.zip"
            }
        )
    }
    ExtractSolutions = @(
        [PSCustomObject]@{
            ZipFile = "$projectRoot\temp\export\Reports.zip"
            PackageType = 'Both'
            Folder = "$projectRoot\data\solutions\Reports"
        }
    )
}