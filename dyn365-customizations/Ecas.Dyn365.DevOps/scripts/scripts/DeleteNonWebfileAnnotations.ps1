param (
    [Parameter(Mandatory=$true)]
    [string]$Path
)

foreach($record in Get-ChildItem -Path (Join-Path -Path $Path -ChildPath annotation\records\*.xml)) {
    $xml = [xml](Get-Content -Path $record.FullName -Raw)

    $objectid = $xml.record.field | where name -EQ 'objectid'
    $documentbody = $xml.record.field | where name -EQ 'documentbody'
    if($objectid -and $objectid.lookupentity -ne 'adx_webfile') {
        Write-Verbose -Message "Deleting annotation $($record.Name) and its associated file attachment" -Verbose:$VerbosePreference
        Remove-Item -Path $record.FullName -Verbose:$VerbosePreference
        if($documentbody) {
            Write-Verbose -Message "Deleting annotation $($record.Name) associated file attachment" -Verbose:$VerbosePreference
            Remove-Item -Path (Join-Path -Path $Path -ChildPath ".\annotation\records\$($documentbody.value)") -Verbose:$VerbosePreference
        }
    }
}