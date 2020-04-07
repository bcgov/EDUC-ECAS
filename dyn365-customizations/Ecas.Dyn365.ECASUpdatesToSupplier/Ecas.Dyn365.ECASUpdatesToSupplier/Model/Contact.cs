using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.ECASUpdatesToSupplier.Model
{
    class Contact
    {
        //Schema Names
        public static string FULL_NAME = "fullname";
        public static string SUPPLIER_NUMBER = "educ_suppliernumber";
        public static string SUPPLIER_SITE_NUMBER = "educ_suppliersitenumber";
        public static string PARTY_ID = "educ_t4apartyid";
        public static string ID = "contactid";
        public static string SUPPLIER_STATUS = "educ_supplierissue";
        public static string STATE_CODE = "statecode";

        public static string ENTITY_NAME = "contact";


        /// <summary>
        ///  Supplier Issue status / Supplier Status Field OptionSet Values
        /// </summary>
        public class SUPPLIER_STATUSES
        {
            public static int NEW_CAS_USER = 610410000; //New CAS User
            public static int UPDATE_REQUESTED = 610410003; //Update Requested
            public static int SUPPLIER_VERIFIED = 610410004; //Supplier Verified
            public static int T4A_ERROR_OCCURRED = 610410005; //T4A Error Occurred
        }



    }
}
