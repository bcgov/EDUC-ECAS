using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Client;
using Microsoft.Xrm.Sdk.Query;
using Microsoft.Xrm.Sdk.Workflow;
using System;
using System.Activities;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.Workflows
{
    public class GetFiscalYearStartDate : WorkFlowActivityBase
    {
        [Input("Fiscal Year Start Date")]
        [Output("Fiscal Year Start Date - Dynamics Config")]
        public InOutArgument<DateTime> FiscalYearStartDate { get; set; }

        [Input("Fiscal Year")]
        [Output("Fiscal Year - Dynamics Config")]
        public InOutArgument<string> FiscalYear { get; set; }

        public override void ExecuteCRMWorkFlowActivity(CodeActivityContext context, LocalWorkflowContext crmWorkflowContext)
        {
            //Retrieve organization
            Entity enOrganization = new Entity("organization");
            EntityCollection ecOrganizations = new EntityCollection();

            DateTime dtFiscal = new DateTime();
            string fiscalYearDisplayValue = string.Empty;
            string fiscalYearFormat = "yy";

            QueryExpression qx = new QueryExpression();
            qx.EntityName = "organization";
            qx.ColumnSet.AllColumns = true;
            ecOrganizations = crmWorkflowContext.OrganizationService.RetrieveMultiple(qx);

            if (ecOrganizations.Entities.Count > 0)
            {
                enOrganization = ecOrganizations.Entities[0];
                dtFiscal = enOrganization.GetAttributeValue<DateTime>("fiscalcalendarstart");
                crmWorkflowContext.TracingService.Trace("Loaded Start Date");

                if (enOrganization.GetAttributeValue<OptionSetValue>("fiscalyearformatprefix").Value == 1)
                    fiscalYearDisplayValue += "FY";

                crmWorkflowContext.TracingService.Trace("Loaded Prefix");

                if (enOrganization.GetAttributeValue<OptionSetValue>("fiscalyearformatyear").Value == 1)
                    fiscalYearFormat = "yyyy";
                else if (enOrganization.GetAttributeValue<OptionSetValue>("fiscalyearformatyear").Value == 2)
                    fiscalYearFormat = "yy";
                else if (enOrganization.GetAttributeValue<OptionSetValue>("fiscalyearformatyear").Value == 3)
                    fiscalYearFormat = "ggyy";

                crmWorkflowContext.TracingService.Trace("Loaded Year Format : " + fiscalYearFormat);

                if (enOrganization.GetAttributeValue<int>("fiscalyeardisplaycode") == 2)
                    fiscalYearDisplayValue += string.Format("{0:" + fiscalYearFormat +"}", dtFiscal.AddYears(1));
                else
                    fiscalYearDisplayValue += string.Format("{0:" + fiscalYearFormat + "}", dtFiscal);

                crmWorkflowContext.TracingService.Trace("Loaded Year Display Code");

                if (enOrganization.GetAttributeValue<OptionSetValue>("fiscalyearformatsuffix").Value == 1)
                    fiscalYearDisplayValue += "FY";
                else if (enOrganization.GetAttributeValue<OptionSetValue>("fiscalyearformatsuffix").Value == 2)
                    fiscalYearDisplayValue += "Fiscal Year";

                crmWorkflowContext.TracingService.Trace("Loaded Suffix");
            }

            FiscalYearStartDate.Set(context, dtFiscal);
            FiscalYear.Set(context, fiscalYearDisplayValue);
        }
    }
}
