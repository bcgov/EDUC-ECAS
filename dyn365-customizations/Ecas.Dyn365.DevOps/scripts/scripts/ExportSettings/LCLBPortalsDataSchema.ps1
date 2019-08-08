param (
    [Parameter(Mandatory)]
    [ValidateNotNullOrEmpty()]
    [hashtable]
    $CrmConnectionParameters
)

$projectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)

@{
	CrmSchemaSettings = [PSCustomObject]@{
        Path = "$projectRoot\Data\Schema\LiquorDEVSchema.xml"
        Destination = "$projectRoot\Data\Schema\LiquorDEVSchema2.xml"
        EntityFilter = { $true } # {$_.name -in 'account','contact'} # only export account and contact
        DisableAllEntityPlugins = $true # disable all plugins on all entities (or use DisableEntityPluginsFilter, don't use both)
        #DisableEntityPluginsFilter = {$_.name -in 'account','contact'} # only disable plugins on account and contact during import
        UpdateComparePrimaryIdFilter = {$_.name -notin 'account','contact'} # all entities except for account and contact will be set to match on their primaryid field during import
        UpdateComparePrimaryNameFilter = {$_.name -in 'uom','uomschedule'} # only uom and uomschedule will be set to match on their primaryname field during import
        EntityUpdateCompareFields = @{
            abs_autonumberedentity = 'abs_entitylogicalname' # array of field names to match on
            incident = 'title','ticketnumber' # array of field names to match on
        }
        FieldFilter = {$_.name -notin 'createdby','createdon','createdonbehalfby','importsequencenumber','modifiedby','modifiedon','modifiedonbehalfby','organizationid','overriddencreatedon','ownerid','owningbusinessunit','owningteam','owninguser','timezoneruleversionnumber','utcconversiontimezonecode','versionnumber'} # include all but these fields on all entities
        EntityFieldFilters = @{
            contact = {$_.name -like 'adx_*' -or $_ -in 'contactid','createdon'} # export all fields that start with adx_ or the fields contactid and createdon
            team = {$_.name -notin 'businessunitid','teamid','name','isdefault'} # exclude all fields except businessunitid, teamid, name, and isdefault'
            businessunit = {$_.name -notin 'businessunitid','name'} # exclude all fields except businessunitid and name
            account = {$_ -in 'accountid','parentaccountid'} #  only export accountid and parentaccountid fields
        }
    }
}