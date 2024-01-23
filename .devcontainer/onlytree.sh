#!/bin/bash
apt install tree

echo "checking workspaces"
ls -a /workspaces
tree /workspaces -L 2 -a

echo "checking root"
ls -a /
