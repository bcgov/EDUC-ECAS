generateAssignmentPayments = function () {
    var parameters = {};
    var assignmentId = Xrm.Page.data.entity.getId();
    assignmentId = assignmentId.replace(/[{}]/g, "");

    var apiAction = "educ_assignments(" + assignmentId + ")/Microsoft.Dynamics.CRM.educ_AssignmentGeneratePaymentRecords";
    var fullUri = Xrm.Page.context.getClientUrl() + "/api/data/v9.0/" + apiAction;

    console.log(fullUri);

    var req = new XMLHttpRequest();
    req.open("POST", fullUri, true);
    req.setRequestHeader("OData-MaxVersion", "4.0");
    req.setRequestHeader("OData-Version", "4.0");
    req.setRequestHeader("Accept", "application/json");
    req.setRequestHeader("Content-Type", "application/json; charset=utf-8");
    req.onreadystatechange = function () {
        if (this.readyState === 4) {
            req.onreadystatechange = null;
            if (this.status === 200 || this.status === 204) {
                //var results = JSON.parse(this.response);

                Xrm.Utility.alertDialog("Payment records generation initiated.");
                /*Xrm.Utility.openEntityForm(Xrm.Page.data.entity.getEntityName(),
                    Xrm.Page.data.entity.getId());*/
            } else {
                Xrm.Utility.alertDialog(this.statusText);
            }
        }
    };
    req.send(JSON.stringify(parameters));
};

displayPaymentGenerationConfirmationDialog = function () {
    var confirmStrings = {
        text: "Upon confirmation all APPROVED expenses and fees will be queued up for payment processing. If you would like to proceed please click 'Confirm'.",
        confirmButtonLabel: "Confirm",
        cancelButtonLabel: "Cancel",
        title: "Generate Assignment Payment Records"
    };
    var confirmOptions = { height: 200, width: 450 };
    Xrm.Navigation.openConfirmDialog(confirmStrings, confirmOptions).then(
        function (success) {
            if (success.confirmed)
                generateAssignmentPayments();
            //else
            //    console.log("Dialog closed using Cancel button or X.");
        });
};