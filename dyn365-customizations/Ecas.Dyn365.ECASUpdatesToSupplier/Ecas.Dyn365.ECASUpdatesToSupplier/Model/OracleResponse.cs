using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.ECASUpdatesToSupplier.Model
{
    class OracleResponse
    {

        int partyId;
        string supplierNumber = string.Empty;
        string siteNumber = string.Empty;
        string statusCode = string.Empty;
        string statusT4A = string.Empty;
        string transactionMessage = string.Empty;
        string tranactionCode = string.Empty;

        Guid contactId = Guid.Empty;

        public int PartyId { get => partyId; set => partyId = value; }
        public string SupplierNumber { get => supplierNumber; set => supplierNumber = value; }
        public string SiteNumber { get => siteNumber; set => siteNumber = value; }
        public string StatusCode { get => statusCode; set => statusCode = value; }
        public string StatusT4A { get => statusT4A; set => statusT4A = value; }
        public string TransactionMessage { get => transactionMessage; set => transactionMessage = value; }
        public Guid ContactId { get => contactId; set => contactId = value; }
        public string TranactionCode { get => tranactionCode; set => tranactionCode = value; }

        internal class T4A_STATUS
        {
            public static string ERROR = "FER";
            public static string OK = "FOK";
        }

        public override string ToString()
        {

            //IMplement this
            return String.Empty;
        }

    }
}
