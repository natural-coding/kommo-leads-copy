## kommo-leads-copy
Sending requests to Kommo API in order to clone leads records deep

![App screen and script logs][app-main-screen]

[app-main-screen]: https://github.com/natural-coding/kommo-leads-copy/blob/main/project-management/screenshots/for-readme/app-screens-all.png

### Project folders

- [**src**](./app) => Web application
- [**project-management**](./project-management)
   - [**!transactions-and-CAP-theorem**](./project-management/!transactions-and-CAP-theorem) => Notes about it ;-)
   - [**amocrm-support**](./project-management/amocrm-support) => An unfinished letter to the support team :-)
   - [**doc**](./project-management/doc) => Kommo API classes documentation I have created (for easy coding! :-))
   - [**doc_src_copy**](./project-management/doc_src_copy) => Copies frequently used files in Kommo API
   - [**screenshots**](./project-management/screenshots) => Self-explained!!
   - [**tricks**](./project-management/tricks) => Tips and tricks related to Kommo interface
   - [timeline.txt](./project-management/timeline.txt) => Local 'commit' names

### Requirement specification

Write a web application which do the following:
1. It should select leads to modification according to certain conditions and its status. Then it should change the status of all leads selected.
2. It should select (another) leads again. It should make the copy (deep clone) of selected leads records including notes and tasks that are connected to.

### Implementation notes

I used the [composition over inheritance approach](https://www.youtube.com/watch?v=wfMtDGfHWpA "funfunfunction youtube channel"). According to this I have created [LeadsCollectionCopier](./app/src/AmoCloud/LeadsCollectionCopier.php), [NotesCollectionCopierAndLinker](./app/src/AmoCloud/NotesCollectionCopierAndLinker.php) and [TasksCollectionCopierAndLinker](./app/src/AmoCloud/TasksCollectionCopierAndLinker.php) classes, that do the job pretty [well](./app/webroot/leads_clone_deep_with_new_status.php). This approach allow us to have a clean project architecture and the clean code! Hello [Robert Martin](https://www.amazon.com/Clean-Code-Handbook-Software-Craftsmanship/dp/0132350882 "Robert Martin Clean Code book")! :-)