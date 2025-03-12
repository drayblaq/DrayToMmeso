/*
** EPITECH PROJECT, 2022
** my_ftp.h
** File description:
** my_ftp.h
*/

#include "myftp.h"

void command_help(one_t *afiss, int k, char *buffer)
{
    dprintf(afiss->tab_client_socket[k], "214 Help message.\r\n");
}

void command_pasv(one_t *afiss, int k, char *buffer)
{
    dprintf(afiss->tab_client_socket[k], "227\r\n");
    afiss->pasv = 1;
}

void command_port(one_t *afiss, int k, char *buffer)
{
    dprintf(afiss->tab_client_socket[k], "200\r\n");
    afiss->pasv = 0;
}

void command_list(one_t *afiss, int k, char *buffer)
{
    DIR *doss;
    struct dirent *dir;

    doss = opendir(".");
    dir = readdir(doss);
    if (doss && afiss->pasv == 1) {
        while (dir != NULL) {
            dprintf(afiss->tab_client_socket[k], dir->d_name);
            dprintf(afiss->tab_client_socket[k], "\n");
            dir = readdir(doss);
        }
        closedir(doss);
        dprintf(afiss->tab_client_socket[k], "226 LIST statut ok\r\n");
    }
    if (afiss->pasv != 1)
        dprintf(afiss->tab_client_socket[k], "425\r\n");
}

void comman2(one_t *afiss, int i, char *buffer)
{
    if (strncmp(buffer, "PORT", 4) == 0) {
        command_port(afiss, i, buffer);
        return;
    }
    if (strncmp(buffer, "PASV", 4) == 0) {
        command_pasv(afiss, i, buffer);
        return;
    }
    if (strncmp(buffer, "LIST", 4) == 0) {
        command_list(afiss, i, buffer);
        return;
    }
    if (strncmp(buffer, "CWD", 3) == 0) {
        command_cwd(afiss, i, buffer);
        return;
    }
}
