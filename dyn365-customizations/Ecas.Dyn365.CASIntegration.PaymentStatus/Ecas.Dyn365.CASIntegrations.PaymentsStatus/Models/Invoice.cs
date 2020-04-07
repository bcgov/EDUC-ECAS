using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using Newtonsoft.Json;

namespace Ecas.Dyn365.CASIntegrations.PaymentsStatus.Models
{
    class Invoice
    {
        public string InvoiceNumber { get; set; }
        public string SupplierNumber { get; set; }
        public string SupplierSiteNumber { get; set; }

        [JsonIgnore]
        public Guid PaymentID { get; set; }

        public string ToJSONString()
        {
            return string.Format("$!$\r\n \"invoiceNumber\": \"{0}\",\r\n \"supplierNumber\": \"{1}\",\r\n \"supplierSiteNumber\": \"{2}\",\r\n  $&$]\r\n$&$",
                InvoiceNumber,
                SupplierNumber,
                SupplierSiteNumber
                ).Replace("$!$", "{").Replace("$&$", "}");
        }
    }
}
