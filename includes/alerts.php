<?php
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Display success messages     
    if (isset($_SESSION['success_message'])) {     
        echo '<div class="modal modal-alert fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content bg-success text-white">
                        <div class="modal-body text-center position-relative">
                            ' . htmlspecialchars($_SESSION['success_message']) . '
                            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.querySelector(".btn-close").addEventListener("click", function() {
                    this.closest(".modal").remove();
                });
            </script>';
        unset($_SESSION['success_message']); // Clear message after displaying     
    }      

    // Display error messages     
    if (isset($_SESSION['error_message'])) {     
        echo '<div class="modal modal-alert fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content bg-danger text-white">
                        <div class="modal-body text-center position-relative">
                            ' . htmlspecialchars($_SESSION['error_message']) . '
                            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.querySelector(".btn-close").addEventListener("click", function() {
                    this.closest(".modal").remove();
                });
            </script>';
        unset($_SESSION['error_message']); // Clear message after displaying     
    } 
?>

