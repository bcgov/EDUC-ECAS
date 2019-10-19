using Ecas.Dyn365.Workflows.Models;
using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Query;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.Workflows.Utils
{
    public class SupplierInformation
    {
        IOrganizationService organizationService;
        ITracingService tracingService;

        public SupplierInformation(IOrganizationService _organizationService, ITracingService _tracingService)
        {
            if (_organizationService == null) throw new ArgumentNullException("Organization Service cannot be null");
            if (_tracingService == null) throw new ArgumentNullException("Tracing Service Id cannot be null");

            organizationService = _organizationService;
            tracingService = _tracingService;

            tracingService.Trace("Loaded Supplier Information Util");
        }

        public Supplier GetSupplierInformation(string socialInsuranceNumber)
        {
            socialInsuranceNumber = socialInsuranceNumber.Trim().Replace(" ", "").Replace(".", "").Replace("-", "");
            tracingService.Trace($"Searching Supplier Infor for SIN: {socialInsuranceNumber}");
            var results = GetCASSupplierEntity(socialInsuranceNumber);
            Supplier supplierInformation = new Supplier();

            tracingService.Trace($"Found {results.Entities.Count} records"); 

            foreach (var supplierNumber in results.Entities)
            {
                supplierInformation.LastName = supplierNumber.GetAttributeValue<string>("educ_name");
                supplierInformation.SupplierNumber = supplierNumber.GetAttributeValue<string>("educ_suppliernumber");
                supplierInformation.SupplierSiteNumber = supplierNumber.GetAttributeValue<string>("educ_suppliersitenumber");
                supplierInformation.MethodOfPayment = supplierNumber.GetAttributeValue<string>("educ_methodofpayment");
            }

            return supplierInformation;
        }

        private EntityCollection GetCASSupplierEntity(string socialInsuranceNumber)
        {
            QueryExpression qx = new QueryExpression();
            qx.EntityName = "educ_cassupplierlookup";
            qx.ColumnSet.AllColumns = true;
            qx.Criteria.AddCondition("educ_name", ConditionOperator.Equal, socialInsuranceNumber);
            qx.Criteria.AddCondition("statecode", ConditionOperator.Equal, 0);

            return organizationService.RetrieveMultiple(qx);
        }
    }
}
