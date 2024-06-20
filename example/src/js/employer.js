document.addEventListener("DOMContentLoaded", function() {
    const profileSettingsLink = document.getElementById("profileSettingsLink");
    const myJobsLink = document.getElementById("myJobsLink");
    const profileForm = document.querySelector(".profile-content");
    const myJobs = document.getElementById("myJobs");

    profileSettingsLink.addEventListener("click", function(event) {
        event.preventDefault();
        profileForm.style.display = "block";
        myJobs.style.display = "none";
    });

    myJobsLink.addEventListener("click", function(event) {
        event.preventDefault();
        profileForm.style.display = "none";
        myJobs.style.display = "block";
    });
});

function enableEdit(fieldId) {
    document.getElementById(fieldId).disabled = false;
}

function uploadProfileImage() {
    const profileImageUpload = document.getElementById("profileImageUpload");
    const profileImage = document.getElementById("profileImage");

    if (profileImageUpload.files && profileImageUpload.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            profileImage.src = e.target.result;
        };

        reader.readAsDataURL(profileImageUpload.files[0]);
    }
}
