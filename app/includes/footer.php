            </main>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="<?= $base_url ?>/app/assets/js/vendor/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="<?= $base_url ?>/app/assets/js/vendor/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="<?= $base_url ?>/app/assets/js/vendor/jquery.dataTables.min.js"></script>
    <script src="<?= $base_url ?>/app/assets/js/vendor/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="<?= $base_url ?>/app/assets/js/vendor/sweetalert2.all.min.js"></script>
    
    <!-- Chart.js -->
    <script src="<?= $base_url ?>/app/assets/js/vendor/chart.umd.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= $base_url ?>/app/assets/js/script.js"></script>
    
    <!-- Flash Message -->
    <?php if (isset($_SESSION['flash_message'])): ?>
    <script>
        Swal.fire({
            icon: '<?= $_SESSION['flash_type'] ?? 'success' ?>',
            title: '<?= $_SESSION['flash_type'] === 'error' ? 'Oops!' : 'Berhasil!' ?>',
            text: '<?= $_SESSION['flash_message'] ?>',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
    <?php 
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    endif; 
    ?>
</body>
</html>
