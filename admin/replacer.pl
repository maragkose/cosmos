#!/usr/bin/perl
###############################################################################
#
# Author: Manolis Maragkos.
# 
# The following script replaces one string with another like sed but in multiple
# files using the inplace editing feature of perl. 
# Original files are backed up y postfixing .bk at the end of the filename
# 
# Example: replacer.pl --replace hello --with HELLO --global *.txt
#
# If the replace or the with options are not used the script will ask the user 
# to input these strings.
#
################################################################################ 

 ####   #####  ####    ##       ####  ####  ####    ##   ####  ###### 
 ## ##  ##     ## ##   ##      ##    ##  #  ## ##   ##   ## ##   ##  
 ## ##  ####   ## ##   ##      ####  ##     ## ##   ##   ## ##   ##   
 ####   ##     ####    ##        ### ##     ####    ##   ####    ##   
 ##     ##     ## ##   ##         ## ##  #  ## ##   ##   ##      ## 
 ##     #####  ##  ##  #####   ####   ###   ##  ##  ##   ##      ##  

use Getopt::Long;
use Net::FTP;

sub ask {
    
    my $type = shift;
    my $str = "";
    my $message = "";

    if ($type eq "replace"){
        $message = "Enter string to find    :";
            for(;;) {
                print $message;
                $str = <STDIN>;             # Get input
                chomp $str;                 # Remove the newline at end
                if ( $str eq ""){
                    print "Empty string not allowed, try again\n";
                }
                last if ($str ne "");
            }
    } else {
        $message = "Enter string to replace :";
    }
    return ($str);
}

$flag="";

$options = GetOptions ( "replace=s"=>\$replace,
                        "with=s"=>\$with,
                        "global"=>\$global);
if($replace eq "" )     { $replace  = &ask("replace"); }
if($with eq "" )        { $with  = &ask ("with"); }
if($global == 1)        { $flag='g'; }


print "\nFiles to edit: ($replace -> $with ) \n";
print "========================================\n";
foreach $file (@ARGV){
    print "$file\n";
}
print "========================================\n";


foreach $file (@ARGV){
    $^I=".bk";              # let the magic begin
    while (<>) {
        if($global) {
            s/$replace/$with/g;    # another new function sneaked in
        } else {
            s/$replace/$with/;    # another new function sneaked in
        }
        print;          # this goes to the temp filehandle, ARGVOUT, 
    }
}

print "\nDone! Original files are backed up.\n";

