
INSERT INTO `Users` (`ID`, `Name`, `Hash`, `Verification`, `Email`, `DateJoined`, `Level`, `NameSafe`) VALUES
(1, 'WildfireXIII', 'c1fa0745402bdb856a6cdf5916b23fcb19a0e6b244d710c7a32aa2f3f36ff05578d8c95f736267f8bb89349f023d2644f0919682465d1075aff0ff327f8f8a23', 'c0e73a79510d01835877b484ddbb87ee42b34951051310d81438f449cec9713aaf843456b9a7414085adc905c29c1e926adf4fc630c7a4a520a66e6e20aac728', 'nmblenderdude0@gmail.com', '2017-12-27', 2, 'wildfirexiii'),
(2, 'Darkar', 'c1fa0745402bdb856a6cdf5916b23fcb19a0e6b244d710c7a32aa2f3f36ff05578d8c95f736267f8bb89349f023d2644f0919682465d1075aff0ff327f8f8a23', '412faeede740661fbb6e36bbb1de5980822d554ce19cd3061268ee1cb89f6c97bf3c901f7ac2841c2ddb7c0caf3a66dde073b9b9193943db6631eed6798afa4e', 'darkardengeno@gmail.com', '2017-12-27', 2, 'darkar');

ALTER TABLE `Users`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
