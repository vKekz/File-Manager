import { Component } from "@angular/core";
import { UserAuthFormComponent } from "../user-auth-form/user-auth-form.component";
import { AuthFormEnum } from "../../../enums/auth-form.enum";

@Component({
  selector: "app-login-form",
  imports: [UserAuthFormComponent],
  templateUrl: "./login-form.component.html",
  styleUrl: "./login-form.component.css",
})
export class LoginFormComponent {
  protected readonly AuthFormEnum = AuthFormEnum;
}
