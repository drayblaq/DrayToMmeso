/*
** EPITECH PROJECT, 2022
** my_ftp.h
** File description:
** my_ftp.h
*/

#include "myftp.h"

void delete(one_t *afiss, int k, char *buffer)
{
    char *pathname = strtok(buffer + 5, "\r\n");

    if (remove(pathname) == 0) {
        dprintf(afiss->tab_client_socket[k], "250\r\n");
    } else
        dprintf(afiss->tab_client_socket[k], "550\r\n");
}

void command_cwd(one_t *afiss, int k, char *buffer)
{
    char *message = NULL;
    char *pathname = strtok(buffer + 4, " \r\n");

    if (chdir(pathname) == 0) {
        message = "250 Directory successfully changed.\r\n";
        dprintf(afiss->tab_client_socket[k], message);
    } else {
        message = "550 Failed to change directory.\r\n";
        dprintf(afiss->tab_client_socket[k], message);
    }
}

void command_cdup(one_t *afiss, int k)
{
    char path[2000];

    getcwd(path, sizeof(path));
    if (strcmp(path, afiss->dossier) != 0) {
        if (chdir("..") == 0) {
            dprintf(afiss->tab_client_socket[k], "250\r\n");
        } else
            dprintf(afiss->tab_client_socket[k], "550\r\n");
    } else
        dprintf(afiss->tab_client_socket[k], "250\r\n");
}

void command3(one_t *afiss, int i, char *buffer)
{
    if (strncmp(buffer, "CDUP", 4) == 0) {
        command_cdup(afiss, i);
        return;
    }
    if (strncmp(buffer, "DELE ", 5) == 0) {
        delete(afiss, i, buffer);
        return;
    }
    if (strncmp(buffer, "PWD", 3) == 0) {
        command_pwd(afiss, i, buffer);
        return;
    }
    if (strncmp(buffer, "HELP", 4) == 0) {
        command_help(afiss, i, buffer);
        return;
    }
    if (strncmp(buffer, "NOOP", 4) == 0) {
        noop(afiss, i, buffer);
        return;
    }
}

void eror(one_t *afiss, char *buffer, int k)
{
    eror2(afiss, buffer, k);
    if (strncmp(buffer, "USER", 4) != 0
        && strncmp(buffer, "PASS", 4) != 0
        && strncmp(buffer, "QUIT", 4) != 0
        && afiss->stat != 1) {
        dprintf(afiss->tab_client_socket[k], "530\r\n");
    } else {
        comman(afiss, k, buffer);
        comman2(afiss, k, buffer);
        command3(afiss, k, buffer);
        comman4(afiss, k, buffer);
    }
}
