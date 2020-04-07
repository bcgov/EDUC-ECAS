using Ecas.Dyn365.Workflows.Utils;
using Microsoft.Xrm.Sdk;
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

        [Output("CAS Supplier Lookup Ref")]
        [ReferenceTarget("educ_cassupplierlookup")]
        public OutArgument<EntityReference> CASSupplierRef { get; set; }

        [Output("Party ID")]
        public OutArgument<int> PartyId { get; set; }

        [Input("Last Name")]
        [Output("Last Name from CAS")]
        public InOutArgument<string> LastName { get; set; }

        [Output("Supplier Number")]
        [Default("-1")]
        public OutArgument<string> SupplierNumber { get; set; }

        [Output("Site Number")]
        [Default("-1")]
        public OutArgument<string> SiteNumber { get; set; }

        [Output("Method of Payment")]
        public OutArgument<string> MethodOfPayment { get; set; }

 
        public override void ExecuteCRMWorkFlowActivity(CodeActivityContext context, LocalWorkflowContext crmWorkflowContext)
        {
            crmWorkflowContext.TracingService.Trace("Loaded Supplier Information Workflow Activity");

            var supplierInformationUtil = new SupplierInformation(crmWorkflowContext.OrganizationService,
                crmWorkflowContext.TracingService);

            var socialInsuranceNumber = SIN.Get(context);

            if (string.IsNullOrEmpty(socialInsuranceNumber)) throw new ArgumentNullException("Social Insurance Number cannot be null or blank");

            var supplierInfo = supplierInformationUtil.GetSupplierInformation(SIN.Get(context));

            crmWorkflowContext.TracingService.Trace($"SupplierInfo: {supplierInfo.ID}");

            if (supplierInfo.ID == Guid.Empty)
            {
                
                crmWorkflowContext.TracingService.Trace("SIN not found in the CAS Supplier Lookup records");

                //Populate entity reference with random GUID to avoid the workflow crash. Dynamics Bug. Microsoft needs to make fix.
                EntityReference CASSupplierLookupRef = new EntityReference("educ_cassupplierlookup", Guid.NewGuid());

                CASSupplierRef.Set(context, CASSupplierLookupRef);
                crmWorkflowContext.TracingService.Trace($"CASSupplierLookupRef: {supplierInfo.ID}");
                PartyId.Set(context, -1);
            }
            else
            {
                EntityReference CASSupplierLookupRef = new EntityReference("educ_cassupplierlookup", supplierInfo.ID);
                CASSupplierRef.Set(context, CASSupplierLookupRef);
                crmWorkflowContext.TracingService.Trace($"CASSupplierLookupRef: {supplierInfo.ID}");

                PartyId.Set(context, supplierInfo.PartyID);
                crmWorkflowContext.TracingService.Trace($"PartyID: {supplierInfo.PartyID}");

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
}
