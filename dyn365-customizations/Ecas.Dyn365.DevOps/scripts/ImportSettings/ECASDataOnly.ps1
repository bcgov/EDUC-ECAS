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


@{
	ExtractedData = [PSCustomObject]@{
        Folder = "$projectRoot\data\LCLBPortal"
        ZipFile = "$projectRoot\temp\packed\data\LCLBPortalData.zip"
        
    }
}