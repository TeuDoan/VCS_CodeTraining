function editMessage(messageId) {
    // Get elements
    let msgText = document.getElementById("msg_test_" + messageId);
    let msgInput = document.getElementById("msg_edit_" + messageId);
    let editButton = document.querySelector(`button[onclick='editMessage(${messageId})']`);
    let saveButton = document.querySelector(`button[onclick='saveMessage(${messageId})']`);
    let cancelButton = document.querySelector(`button[onclick='cancelEdit(${messageId})']`);

    // Show input and action buttons
    msgText.style.display = "none";
    msgInput.style.display = "inline";
    saveButton.style.display = "inline";
    cancelButton.style.display = "inline";
    editButton.style.display = "none";
}

function cancelEdit(messageId) {
    let msgText = document.getElementById("msg_test_" + messageId);
    let msgInput = document.getElementById("msg_edit_" + messageId);
    let editButton = document.querySelector(`button[onclick='editMessage(${messageId})']`);
    let saveButton = document.querySelector(`button[onclick='saveMessage(${messageId})']`);
    let cancelButton = document.querySelector(`button[onclick='cancelEdit(${messageId})']`);

    // Hide input and show original message
    msgText.style.display = "inline";
    msgInput.style.display = "none";
    saveButton.style.display = "none";
    cancelButton.style.display = "none";
    editButton.style.display = "inline";
}

function saveMessage(messageId) {
    let newMessage = document.getElementById("msg_edit_" + messageId).value;

    // Send AJAX request to update message
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "message.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText.trim() === "Success") {
                // Update UI with new message
                document.getElementById("msg_test_" + messageId).innerText = newMessage;
                cancelEdit(messageId);
            } else {
                alert("Error: " + xhr.responseText);
            }
        }
    };

    xhr.send("id=" + messageId + "&message=" + encodeURIComponent(newMessage));
}
