##
## EPITECH PROJECT, 2022
## Makefile
## File description:
## Makefile
##

SRC	=	$(wildcard *.c)

OBJ	=	$(SRC:.c=.o)

NAME	=	myftp

all:	$(NAME)

$(NAME):	$(OBJ)
	 gcc -o $(NAME) $(OBJ)
clean:
	rm -f $(OBJ)

fclean:	clean
	rm -f $(NAME)

re:	fclean all
