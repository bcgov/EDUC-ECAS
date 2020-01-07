@{
    OrganizationName = 'LCLBCannabisUAT'
    ServerUrl = 'https://reveal.test.jag.gov.bc.ca'
    Credential = [PSCredential]::new("idir\andchan", ("M@ySummer2018" | ConvertTo-SecureString -AsPlainText -Force))
}