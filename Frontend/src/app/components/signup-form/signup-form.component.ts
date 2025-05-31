import { Component } from "@angular/core";
import { UserAuthFormComponent } from "../user-auth-form/user-auth-form.component";
import { AuthFormEnum } from "../../../enums/auth-form.enum";

@Component({
  selector: "app-signup-form",
  imports: [UserAuthFormComponent],
  templateUrl: "./signup-form.component.html",
  styleUrl: "./signup-form.component.css",
})
export class SignupFormComponent {
  protected readonly AuthFormEnum = AuthFormEnum;
}
