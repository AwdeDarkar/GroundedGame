
INSERT INTO `Users` (`ID`, `Name`, `Hash`, `Verification`, `Email`, `DateJoined`, `Level`, `NameSafe`) VALUES
(1, 'WildfireXIII', 'c1fa0745402bdb856a6cdf5916b23fcb19a0e6b244d710c7a32aa2f3f36ff05578d8c95f736267f8bb89349f023d2644f0919682465d1075aff0ff327f8f8a23', 'c0e73a79510d01835877b484ddbb87ee42b34951051310d81438f449cec9713aaf843456b9a7414085adc905c29c1e926adf4fc630c7a4a520a66e6e20aac728', 'nmblenderdude0@gmail.com', '2017-12-27', 2, 'wildfirexiii');

ALTER TABLE `Users`
  MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
