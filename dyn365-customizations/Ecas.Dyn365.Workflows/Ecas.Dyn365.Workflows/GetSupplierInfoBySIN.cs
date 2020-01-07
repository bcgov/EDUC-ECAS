using Ecas.Dyn365.Workflows.Utils;
using Microsoft.Xrm.Sdk.Workflow;
using System;
using System.Activities;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.Workflows
{
    public class GetSupplierInfoBySIN : WorkFlowActivityBase
    {
        [Input("Social Insurance Number")]
        public InArgument<string> SIN{ get; set; }

        [Input("Last Name")]
        [Output("Last Name from CAS")]
        public InOutArgument<string> LastName { get; set; }

        [Output("Supplier Number")]
        public OutArgument<string> SupplierNumber { get; set; }

        [Output("Site Number")]
        public OutArgument<string> SiteNumber { get; set; }

        [Output("Method of Payment")]
        public OutArgument<string> MethodOfPayment { get; set; }

        public override void ExecuteCRMWorkFlowActivity(CodeActivityContext context, LocalWorkflowContext crmWorkflowContext)
        {
            crmWorkflowContext.TracingService.Trace("Loaded Supplier Information Workflow Activity");

            var supplierInformationUtil = new SupplierInformation(crmWorkflowContext.OrganizationService, 
                crmWorkflowContext.TracingService);

            var socialInsuranceNumber = SIN.Get(context);

            if(string.IsNullOrEmpty(socialInsuranceNumber)) throw new ArgumentNullException("Social Insurance Number cannot be null or blank");

            var supplierInfo = supplierInformationUtil.GetSupplierInformation(SIN.Get(context));
            LastName.Set(context, supplierInfo.LastName);
            crmWorkflowContext.TracingService.Trace($"Last Found: {supplierInfo.LastName}");

            SupplierNumber.Set(context, supplierInfo.SupplierNumber);
            crmWorkflowContext.TracingService.Trace($"Supplier Number: {supplierInfo.SupplierNumber}");

            SiteNumber.Set(context, supplierInfo.SupplierSiteNumber);
            crmWorkflowContext.TracingService.Trace($"Supplier Site Number: {supplierInfo.SupplierSiteNumber}");

            MethodOfPayment.Set(context, supplierInfo.MethodOfPayment);
            crmWorkflowContext.TracingService.Trace($"Method of Payment: {supplierInfo.MethodOfPayment}");

            crmWorkflowContext.TracingService.Trace("Custom Workflow Activity Finished");
        }
     }
}
