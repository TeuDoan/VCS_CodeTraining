#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#define FILE_PATH "/etc/passwd"
#define GROUP_FILE "/etc/group"
#define MAX_LINE 1024  // Maximum line length
#define MAX_GROUPS 100 // Maximum number of groups a user might be in

char* check_username(char* line) {
    char *line_copy = strdup(line);
    if (!line_copy) {
        perror("Memory allocation failed");
        exit(1);
    }

    char *username = strtok(line_copy, ":");

    if (!username) {
        free(line_copy);  // Free memory if parsing fails
        return NULL;
    }

    char *result = strdup(username);
    free(line_copy);  // Free copied line before returning

    if (!result) {
        perror("Memory allocation failed");
        exit(1);
    }
    return result;
}

void read_etc_passwd(char* line){
    char *username, *password, *uid, *gid, *fullname, *home, *shell;

    username = strtok(line, ":");
    password = strtok(NULL, ":");
    uid = strtok(NULL, ":");
    gid = strtok(NULL, ":");
    fullname = strtok(NULL, ":");
    home = strtok(NULL, ":");
    shell = strtok(NULL, ":");

    if (!username || !uid || !home) {
        printf("Error: Could not parse line properly.\n");
        return;
    }

    printf("Username: %s\n", username);
    printf("User ID: %s\n", uid);
    printf("Home Directory: %s\n", home);
}

void get_user_groups(const char *username) {
    FILE *file = fopen(GROUP_FILE, "r");
    if (!file) {
        perror("Failed to open /etc/group");
        return;
    }

    char line[MAX_LINE];
    char *groups[MAX_GROUPS];
    int group_count = 0;

    while (fgets(line, sizeof(line), file)) {
        char *group_name = strtok(line, ":");
        strtok(NULL, ":");  
        strtok(NULL, ":");  
        char *users = strtok(NULL, "\n");

        if (users && strstr(users, username)) {
            groups[group_count++] = strdup(group_name);
            if (group_count >= MAX_GROUPS) break;
        }
    }

    fclose(file);

    printf("User '%s' is in the following groups:\n", username);
    for (int i = 0; i < group_count; i++) {
        printf("- %s\n", groups[i]);
        free(groups[i]);  // Free allocated memory
    }
}

int main() {
    char username[256];

    printf("Enter your username: ");
    scanf("%255s", username);  // Prevent buffer overflow

    FILE* file = fopen(FILE_PATH, "r");
    if (!file) {  // Check if file opened successfully
        perror("Failed to open /etc/passwd");
        return 1;
    }

    int user_found = 0;
    char line[MAX_LINE];

    while (fgets(line, sizeof(line), file)) {
        char *precise_username = check_username(line);
        if (precise_username && (strcmp(precise_username, username)==0)) {  // Prevent segmentation fault
            printf("User found!\n");
            user_found = 1;
            read_etc_passwd(line);
            get_user_groups(precise_username);
            free(precise_username);
            printf("\n");
        }
    }

    fclose(file);  // Close file after use

    if (user_found == 0) {
        printf("User not found\n");
    }

    return 0;
}
