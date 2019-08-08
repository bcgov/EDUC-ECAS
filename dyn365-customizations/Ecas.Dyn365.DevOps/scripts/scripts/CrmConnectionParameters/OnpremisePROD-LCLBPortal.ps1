@{
    OrganizationName = 'LCLBCannabis'
    ServerUrl = 'https://lcrb-cllcms.jag.gov.bc.ca'
    Credential = [PSCredential]::new("idir\andchan", ("pass@word1" | ConvertTo-SecureString -AsPlainText -Force))
}