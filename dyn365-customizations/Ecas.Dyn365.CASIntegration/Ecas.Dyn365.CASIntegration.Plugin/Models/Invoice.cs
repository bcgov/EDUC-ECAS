using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace BCGov.Dyn365.CASIntegration.Plugin.Payment
{
    public class Invoice
    {
        public string InvoiceType { get; set; }
        public string SupplierNumber { get; set; }
        public int SupplierSiteNumber { get; set; }
        public DateTime InvoiceDate { get; set; }
        public string InvoiceNumber { get; set; }
        public decimal InvoiceAmount { get; set; }
        public string PayGroup { get; set; }
        public DateTime DateInvoiceReceived { get; set; }
        public DateTime? DateGoodsReceived { get; set; }
        public string RemittanceCode { get; set; }
        public bool SpecialHandling { get; set; }
        public string NameLine1 { get; set; }
        public string NameLine2 { get; set; }
        public string AddressLine1 { get; set; }
        public string AddressLine2 { get; set; }
        public string AddressLine3 { get; set; }
        public string City { get; set; }
        public string Country { get; set; }
        public string Province { get; set; }
        public string PostalCode { get; set; }
        public string QualifiedReceiver { get; set; }
        public string Terms { get; set; }
        public string PayAloneFlag { get; set; }
        public string PaymentAdviceComments { get; set; }
        public string RemittanceMessage1 { get; set; }
        public string RemittanceMessage2 { get; set; }
        public string RemittanceMessage3 { get; set; }
        public DateTime? GLDate { get; set; }
        public string InvoiceBatchName { get; set; }
        public string CurrencyCode { get; set; }
        public int InvoiceLineNumber { get; set; }
        public string InvoiceLineType { get; set; }
        public string LineCode { get; set; }
        public decimal InvoiceLineAmount { get; set; }
        public string DefaultDistributionAccount { get; set; }
        public string Description { get; set; }
        public string TaxClassificationCode { get; set; }
        public string DistributionSupplier { get; set; }
        public string Info1 { get; set; }
        public string Info2 { get; set; }
        public string Info3 { get; set; }

        public string ToJSONString()
        {
            string dateFormat = "dd-MMM-yyyy";
            string amountFormat = "00.00";

            return string.Format("$!$\r\n \"invoiceType\": \"{0}\",\r\n \"supplierNumber\": \"{1}\",\r\n \"supplierSiteNumber\": \"{2}\",\r\n \"invoiceDate\": \"{3}\",\r\n \"invoiceNumber\": \"{4}\",\r\n \"invoiceAmount\": {5},\r\n \"payGroup\": \"{6}\",\r\n \"dateInvoiceReceived\": \"{7}\",\r\n \"dateGoodsReceived\": \"{8}\",\r\n \"remittanceCode\": \"{9}\",\r\n \"specialHandling\": \"{10}\",\r\n \"nameLine1\": \"{11}\",\r\n \"nameLine2\": \"{12}\",\r\n \"addressLine1\": \"{13}\",\r\n \"addressLine2\": \"{14}\",\r\n \"addressLine3\": \"{15}\",\r\n \"city\": \"{16}\",\r\n \"country\": \"{17}\",\r\n \"province\": \"{18}\",\r\n \"postalCode\": \"{19}\",\r\n \"qualifiedReceiver\": \"{20}\",\r\n \"terms\": \"{21}\",\r\n \"payAloneFlag\": \"{22}\",\r\n \"paymentAdviceComments\": \"{23}\",\r\n \"remittanceMessage1\": \"{24}\",\r\n \"remittanceMessage2\": \"{25}\",\r\n \"remittanceMessage3\": \"{26}\",\r\n \"glDate\": \"{27}\",\r\n \"invoiceBatchName\": \"{28}\",\r\n \"currencyCode\": \"{29}\",\r\n \"invoiceLineDetails\": [$!$\r\n   \"invoiceLineNumber\": {30},\r\n   \"invoiceLineType\": \"{31}\",\r\n   \"lineCode\": \"{32}\",\r\n   \"invoiceLineAmount\": {33},\r\n   \"defaultDistributionAccount\": \"{34}\",\r\n   \"description\": \"{35}\",\r\n   \"taxClassificationCode\": \"{36}\",\r\n   \"distributionSupplier\": \"{37}\",\r\n   \"info1\": \"{38}\",\r\n   \"info2\": \"{39}\",\r\n   \"info3\": \"{40}\"\r\n   $&$]\r\n$&$",
                InvoiceType,
                SupplierNumber,
                SupplierSiteNumber.ToString("000"),
                InvoiceDate.ToLocalTime().ToString(dateFormat),
                InvoiceNumber,
                InvoiceAmount.ToString(amountFormat),
                PayGroup,
                DateInvoiceReceived.ToLocalTime().ToString(dateFormat),
                DateGoodsReceived.HasValue ? DateGoodsReceived.Value.ToLocalTime().ToString(dateFormat) : "",
                RemittanceCode,
                (SpecialHandling ? "D" : "N"),
                NameLine1,
                NameLine2,
                AddressLine1,
                AddressLine2,
                AddressLine3,
                City,
                Country,
                Province,
                PostalCode = !string.IsNullOrEmpty(PostalCode) ? PostalCode.Replace(" ", ""): string.Empty,
                QualifiedReceiver,
                Terms,
                PayAloneFlag,
                PaymentAdviceComments,
                RemittanceMessage1,
                RemittanceMessage2,
                RemittanceMessage3,
                GLDate.HasValue ? GLDate.Value.ToLocalTime().ToString(dateFormat) : "",
                InvoiceBatchName,
                CurrencyCode,
                InvoiceLineNumber,
                InvoiceLineType,
                LineCode,
                InvoiceLineAmount.ToString(amountFormat),
                DefaultDistributionAccount,
                Description,
                TaxClassificationCode,
                DistributionSupplier,
                Info1,
                Info2,
                Info3
                ).Replace("$!$", "{").Replace("$&$", "}");
        }
    }
}
