#define _GNU_SOURCE
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <shadow.h>
#include <crypt.h>
#include <pwd.h>
#include <errno.h>

#define SHADOW_FILE "/etc/shadow"

void change_password(const char *username) {
    struct spwd *sp_entry;
    char *old_pass, *new_pass, *confirm_pass;
    char *encrypted_old, *encrypted_new;
    FILE *shadow_file, *temp_file;
    char line[1024];
    int found = 0;

    // Retrieve user information from /etc/shadow
    sp_entry = getspnam(username);
    if (!sp_entry) {
        perror("Error retrieving user information");
        exit(EXIT_FAILURE);
    }

    old_pass = getpass("Enter current password: ");
    encrypted_old = crypt(old_pass, sp_entry->sp_pwdp);
    if (strcmp(encrypted_old, sp_entry->sp_pwdp) != 0) {
        printf("Incorrect current password!\n");
        exit(EXIT_FAILURE);
    }

    new_pass = getpass("Enter new password: ");
    confirm_pass = getpass("Re-enter new password: ");

    if (strcmp(new_pass, confirm_pass) != 0) {
        printf("New passwords do not match!\n");
        exit(EXIT_FAILURE);
    }


    encrypted_new = crypt(new_pass, sp_entry->sp_pwdp);

    // Open shadow file to change password
    shadow_file = fopen(SHADOW_FILE, "r");
    temp_file = fopen("/etc/shadow.tmp", "w");
    if (!shadow_file || !temp_file) {
        perror("Error opening file");
        exit(EXIT_FAILURE);
    }

    while (fgets(line, sizeof(line), shadow_file)) {
        if (strstr(line, username) == line) {
            fprintf(temp_file, "%s:%s:18632:0:99999:7:::\n", username, encrypted_new);
            found = 1;
        } else {
            fputs(line, temp_file);
        }
    }

    fclose(shadow_file);
    fclose(temp_file);

    if (!found) {
        printf("User not found in /etc/shadow\n");
        remove("/etc/shadow.tmp");
        exit(EXIT_FAILURE);
    }

    // Overwrite /etc/shadow file
    if (rename("/etc/shadow.tmp", SHADOW_FILE) != 0) {
        perror("Error updating password");
        exit(EXIT_FAILURE);
    }

    printf("Password changed successfully!\n");
}

int main() {
    uid_t uid = getuid();
    struct passwd *pw = getpwuid(uid);
    
    if (!pw) {
        perror("Unable to retrieve user information");
        return EXIT_FAILURE;
    }

    change_password(pw->pw_name);
    return EXIT_SUCCESS;
}
