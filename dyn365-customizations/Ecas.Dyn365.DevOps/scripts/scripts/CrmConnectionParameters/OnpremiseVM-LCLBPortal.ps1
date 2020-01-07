@{
    OrganizationName = 'SPDDev'
    ServerUrl = 'http://dyn365/'
    Credential = [PSCredential]::new("contoso\administrator", ("pass@word1" | ConvertTo-SecureString -AsPlainText -Force))
}