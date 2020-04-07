using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.Model
{
    class Contact
    {
        private Guid id;
        private int party_id;
        private string first_name;
        private string last_name;
        private string sin;
        private string address1;
        private string payment_method;
        private string city;
        private string country_code;
        private string province_code;
        private string postal_code;

        public Guid ID { get => id; set => id = value; }
        
        public string First_Name { get => first_name; set => first_name = value; }
        public string Last_Name { get => last_name; set => last_name = value; }
        public string SIN { get => sin; set => sin = value; }
        public string Address1 { get => address1; set => address1 = value; }
        public string Payment_Method { get => payment_method; set => payment_method = value; }
        public string City { get => city; set => city = value; }
        public string Country_Code { get => country_code; set => country_code = value; }
        public string Province_Code { get => province_code; set => province_code = value; }
        public string Postal_Code { get => postal_code; set => postal_code = value; }
        public int Party_Id { get => party_id; set => party_id = value; }

        internal class Schema
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

        }


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
