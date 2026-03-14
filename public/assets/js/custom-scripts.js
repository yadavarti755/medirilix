function showLoader() {
    $(".loader").show();
}

function hideLoader() {
    $(".loader").hide();
}

function showFrontEndLoader() {
    $(".site-loader").show();
}

function hideFrontEndLoader() {
    $(".site-loader").hide();
}

function encryptPassword(password, encryptionKey) {
    if (!encryptionKey) {
        console.error("Encryption key is not ready.");
        return null;
    }

    const key = CryptoJS.enc.Hex.parse(encryptionKey);

    // Generate random IV
    const iv = CryptoJS.lib.WordArray.random(16);

    const encrypted = CryptoJS.AES.encrypt(password, key, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7,
    });

    // Combine IV and ciphertext (for backend)
    const encryptedData = iv
        .concat(encrypted.ciphertext)
        .toString(CryptoJS.enc.Base64);
    return encryptedData;
}
