/*
** EPITECH PROJECT, 2022
** my_ftp.h
** File description:
** my_ftp.h
*/

#include "myftp.h"

void waou(char *buffer, int j)
{
    if (buffer[j] == '\r' || buffer[j] == '\n') {
        buffer[j] = '\0';
    }
}

void command_pwd(one_t *afiss, int k, char *buffer)
{
    char *path = getcwd(NULL, 0);

    dprintf(afiss->tab_client_socket[k], "257 ");
    dprintf(afiss->tab_client_socket[k], path);
    dprintf(afiss->tab_client_socket[k], "\r\n");
}

void command_password(one_t *afiss, int k, char *buffer)
{
    char **pass = split(buffer, " \n\r");

    if (afiss->usr != 1) {
        dprintf(afiss->tab_client_socket[k], "530.\r\n");
    } else {
        if (FD_ISSET(afiss->tab_client_socket[k], &afiss->socket) != 1) {
            dprintf(afiss->tab_client_socket[k], "530 erreur.\r\n");
        }
        if (pass[1] == NULL && strcmp(afiss->com, "Anonymous") == 0) {
            afiss->stat = 1;
            dprintf(afiss->tab_client_socket[k], "230 user logged in,");
            dprintf(afiss->tab_client_socket[k], " proceed.\r\n");
        } else
            dprintf(afiss->tab_client_socket[k], "530 n.\r\n");
    }
}

void command_user(one_t *afiss, int k, char *buffer)
{
    char **commande = split(buffer, " \n\r");

    strncpy(afiss->com, commande[1], 50);
    if (strncmp(commande[1], "Anonymous", 9) == 0) {
        FD_SET(afiss->tab_client_socket[k], &afiss->socket);
    }
    dprintf(afiss->tab_client_socket[k], "331 ");
    dprintf(afiss->tab_client_socket[k],
        ": User name okay, need password.\r\n");
    afiss->usr = 1;
}

void command(one_t *afiss, int i)
{
    char buffer[BUFFER];

    if (read(afiss->tab_client_socket[i], buffer, sizeof(buffer)) == -1) {
        close(afiss->tab_client_socket[i]);
    } else {
        for (int j = 0; j < BUFFER; j++) {
            waou(buffer, j);
        }
        eror(afiss, buffer, i);
    }
}
