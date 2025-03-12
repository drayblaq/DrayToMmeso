/*
** EPITECH PROJECT, 2022
** my_ftp.h
** File description:
** my_ftp.h
*/

#include "myftp.h"

void command_retr(one_t *afiss, int k, char *buffer)
{
    dprintf(afiss->tab_client_socket[k], "150 File ok\r\n");
}

void command_stor(one_t *afiss, int k, char *buffer)
{
    dprintf(afiss->tab_client_socket[k], "150 File ok\r\n");
}

void comman4(one_t *afiss, int i, char *buffer)
{
    if (strncmp(buffer, "STOR", 4) == 0) {
        command_stor(afiss, i, buffer);
        return;
    }
    if (strncmp(buffer, "RETR", 4) == 0) {
        command_retr(afiss, i, buffer);
        return;
    }
}
