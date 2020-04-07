using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.CASIntegrations.PaymentsStatus.Models
{
    class PaymentResponse
    {
        public string invoice_number { get; set; }
        public string invoice_status { get; set; }
        public string payment_status { get; set; }
        public string payment_number { get; set; }
        public string payment_date { get; set; }

        public static string NOT_FOUND_STATUS = "Not Found";

        //Default constructor
        public PaymentResponse()
        {

        }

        public override string ToString()
        {
            StringBuilder builder = new StringBuilder();
            builder.AppendLine("CAS Response:")
                .Append("Invoice Number = ")
                .AppendLine(invoice_number)
                .Append("Invoice Status = ")
                .AppendLine(invoice_status)
                .Append("Payment Status = ")
                .AppendLine(payment_status)
                .Append("Payment Date = ")
                .AppendLine(payment_date)
                .Append("End of Response");

            return builder.ToString();
        }

        /// <summary>
        /// Overload of the ToString method to take base text as input and return the formatted string
        /// </summary>
        /// <param name="baseText">Base text to append before the this.ToString()</param>
        /// <returns></returns>
        public string ToString(string baseText)
        {
            return new StringBuilder().AppendLine(baseText).AppendLine().Append(this.ToString()).ToString();
        }

    }

}