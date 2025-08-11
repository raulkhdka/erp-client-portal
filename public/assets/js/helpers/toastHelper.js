function showToast(type = 'success', message = 'Operation successful', options = {}) {
    Swal.fire({
        icon: type,
        title: type.charAt(0).toUpperCase() + type.slice(1),
        text: message,
        toast: true,
        position: options.position || 'top-end',
        timer: options.timer || 3000,
        showConfirmButton: false,
        timerProgressBar: true
    });
}