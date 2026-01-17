# Employee-Management-System-PHP-MySQL-
This project is a simple Employee Management System built with PHP, MySQL, and Bootstrap. It allows users to create, read, update, and delete employee records through a web interface.

Key Features & Best Practices

CRUD Functionality: Users can add, edit, view, and delete employee records.

Pagination: Efficiently displays employees in pages to handle large datasets.

Prepared Statements: All database operations use prepared statements to prevent SQL injection.

XSS Protection: User input and output are sanitized using htmlspecialchars() to prevent cross-site scripting.

CSRF Protection: Deletion and form submissions include CSRF tokens to prevent cross-site request forgery.

Form Validation: Both client-side and server-side validations ensure all fields are correctly filled.

Error Handling: Database errors are captured and displayed for debugging (development mode).

Responsive UI: Built with Bootstrap 5 for mobile-friendly and accessible design.

Security & Industry Practices:

POST methods are enforced for sensitive actions like deletion.

Data is escaped and validated before insertion or update.

Confirmation prompts prevent accidental deletions.

PRG Pattern: Uses the Post/Redirect/Get pattern to prevent form resubmission on page refresh.

This system follows modern web development best practices to ensure security, usability, and maintainability.
