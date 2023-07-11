window.addEventListener("DOMContentLoaded", function () {
   const docList = 
'LeadModel_methods.html\n\
LeadsCollection_methods.html\n\
LeadModel_object.html\n\
LeadModel_object_real_with_empty.html\n\
LeadModel_object_real_with_contacts.html\n\
LeadModel_object_real_toArray.html\n\
LeadModel_object_real_with_contacts_companies.html\n\
LeadModel_object_all_real_with_contacts.html\n\
CommonNote_object_real.html\n\
CommonNote_methods.html\n\
TaskModel_methods.html\n\
TaskModel_object_real.html\n\
TasksFilter_methods.html\n\
NotesFilter_methods.html\n\
AmoCRMApiErrorResponseException.html\n\
LeadsFilter_methods.html';

   // console.log(docList);

   const htmlToAdd = docList
      .split('\n')
      .map(p_item => `<p><a href="${p_item}" target="_blank">${p_item}</a></p>\n`)
      .reduce((p_html,p_item) => p_html + p_item,'');

   const el = document.querySelector('html');
   el.insertAdjacentHTML("afterbegin",htmlToAdd);

});
